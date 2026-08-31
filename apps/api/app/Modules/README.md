# Módulos de negocio

Cada módulo sigue este flujo:

```text
Http Request -> Controller -> Application UseCase -> Domain Repository -> Infrastructure Mapper -> Eloquent
```

- `Http`: adaptadores HTTP; no contiene reglas de negocio ni consultas.
- `Application`: casos de uso y DTOs que coordinan una operación.
- `Domain`: entidades independientes de Laravel y contratos de repositorio.
- `Infrastructure`: implementación Eloquent de los contratos y mapeadores entre modelos y entidades.

Los modelos Eloquent permanecen en `app/Models` porque representan la persistencia compartida. Los módulos no deben acceder a modelos directamente fuera de `Infrastructure`.
