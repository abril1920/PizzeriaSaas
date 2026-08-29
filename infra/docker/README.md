# Docker

Este directorio reserva la configuracion de imagenes para los servicios de la aplicacion.

- `api/`: Dockerfile y archivos de inicio de Laravel.
- `web/`: Dockerfile para la compilacion o entrega de React.
- `nginx/`: configuracion del proxy inverso cuando el entorno lo requiera.

Los servicios que se ejecutan localmente se declaran en `../../compose.yaml`.
