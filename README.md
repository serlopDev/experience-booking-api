# Experience Booking API

Este repositorio contiene la solución desarrollada para la prueba técnica de backend de Nalanda.

La aplicación consiste en una API REST desarrollada en Laravel para gestionar experiencias, sus sesiones y las reservas de plazas.

El proyecto se ha planteado siguiendo una arquitectura hexagonal, manteniendo la lógica de negocio desacoplada de Laravel, Eloquent y la capa HTTP.

## Requisitos

- Docker
- Docker Compose
- Laravel Sail

El proyecto utiliza MySQL como base de datos.

## Instalación

Levantar los contenedores:

```bash
./vendor/bin/sail up -d
```

Ejecutar las migraciones:

```bash
./vendor/bin/sail artisan migrate
```

Para ejecutar los tests:

```bash
./vendor/bin/sail artisan test
```

## API

### Crear una experiencia

```http
POST /api/experiences
```

Ejemplo:

```json
{
    "provider_id": "provider-001",
    "title": "Madrid walking tour",
    "description": "Walking tour through Madrid."
}
```

### Crear una sesión

```http
POST /api/experiences/{experienceId}/sessions
```

Ejemplo:

```json
{
    "starts_at": "2030-10-15T18:00:00+02:00",
    "max_capacity": 20,
    "price_in_cents": 2500
}
```

No se permite crear una sesión en el pasado ni más de una sesión de la misma experiencia en el mismo día.

### Crear una reserva

```http
POST /api/sessions/{sessionId}/reservations
```

Ejemplo:

```json
{
    "user_id": "user-001",
    "contact_email": "user@example.com",
    "seats": 2
}
```

El precio total de la reserva se calcula a partir del precio de la sesión. No se acepta el precio desde la petición.

Una reserva nueva se crea con estado `confirmed`.

### Cancelar una reserva

```http
DELETE /api/reservations/{reservationId}
```

La reserva no se elimina físicamente. Su estado pasa de `confirmed` a `cancelled`.

Al cancelar una reserva, las plazas vuelven a estar disponibles en la sesión.

No se permite:

- cancelar una reserva ya cancelada;
- cancelar durante las 24 horas anteriores al inicio de la sesión.

## Arquitectura

El código está organizado por módulos de negocio:

```text
app/
├── Experience/
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
│
├── Session/
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
│
├── Reservation/
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
│
└── Shared/
    ├── Application/
    └── Infrastructure/
```

### Domain

Contiene las entidades y reglas de negocio.

Esta capa no depende de Laravel, Eloquent, HTTP ni de la base de datos.

Algunos ejemplos de reglas que se encuentran en dominio:

- una sesión debe tener una capacidad válida;
- una sesión no puede reservarse después de haber comenzado;
- no se puede superar la capacidad de una sesión;
- una reserva no puede cancelarse dos veces;
- una reserva no puede cancelarse durante las 24 horas anteriores al inicio.

### Application

Contiene los casos de uso y coordina las operaciones entre entidades y puertos.

Por ejemplo:

```text
CreateExperience
CreateSession
CreateReservation
CancelReservation
```

También se definen aquí dependencias que necesita la aplicación, como `TransactionManager` o el envío de notificaciones, sin depender de una implementación concreta de Laravel.

### Infrastructure

Contiene los detalles relacionados con el framework:

- controllers;
- Form Requests;
- API Resources;
- modelos Eloquent;
- repositorios Eloquent;
- transacciones de Laravel;
- notificaciones;
- Service Providers.

De esta forma, la lógica de negocio no necesita conocer cómo se persisten los datos ni cómo llega una petición HTTP.

## Persistencia

Las entidades de dominio están separadas de los modelos de Eloquent.

Por ejemplo, `Session` representa el comportamiento de negocio de una sesión, mientras que `SessionModel` únicamente representa su persistencia en MySQL.

Los repositorios definidos por la aplicación/dominio se implementan mediante Eloquent en Infrastructure.

## Dinero

Los precios se almacenan como enteros en céntimos:

```text
2500 = 25,00 €
```

Se evita utilizar `float` para no introducir problemas de precisión en operaciones monetarias.

El precio total de una reserva se calcula en dominio:

```text
precio de la sesión × número de plazas
```

## Concurrencia

La creación de reservas debe soportar varias peticiones intentando reservar las últimas plazas de una sesión al mismo tiempo.

Para evitar overbooking se utiliza bloqueo pesimista sobre la fila de la sesión:

```php
lockForUpdate()
```

El bloqueo se realiza dentro de una transacción de base de datos.

De forma simplificada:

```text
BEGIN TRANSACTION

SELECT session FOR UPDATE

comprobar plazas disponibles
actualizar plazas reservadas
crear reserva

COMMIT
```

Mientras una petición mantiene el bloqueo sobre una sesión, otra petición que quiera reservar sobre esa misma sesión debe esperar.

Cuando obtiene el bloqueo, vuelve a leer el estado actualizado y se comprueba nuevamente la capacidad.

La regla de capacidad continúa estando en el dominio; `lockForUpdate()` únicamente garantiza que esa regla se evalúe sobre un estado consistente cuando existen
