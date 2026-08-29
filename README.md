# Pizzeria SaaS

Plataforma SaaS multiempresa para la gestion de pizzerias. El repositorio usa un monorepo ligero: las aplicaciones viven en `apps/`, la infraestructura local en `compose.yaml` e `infra/`, y la documentacion se organiza por dominio.

## Estructura

```text
apps/api/                 Laravel API
apps/web/                 React SPA
database/snapshots/       Referencias SQL historicas
docs/architecture/        Decisiones y diseno tecnico
docs/product/             Requisitos funcionales
infra/docker/             Dockerfiles y configuracion de imagenes
compose.yaml              Servicios locales
```

Las migraciones de `apps/api/database/migrations` son la fuente de verdad del esquema. El contenido de `database/snapshots` no se debe usar para evolucionar la base de datos.

## Desarrollo local

```powershell
docker compose up -d
Set-Location apps/api; composer install; php artisan migrate
Set-Location ../web; npm install; npm run dev
```

Consulta [la arquitectura](docs/architecture/overview.md) y [la definicion del producto](docs/product/project-overview.md) antes de agregar un modulo.
