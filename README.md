# PRUEBA DE DESARROLLO MVP PARA FYNKUS

Se trata de un proyecto construido con Symfony (Arquitectura Hexagonal, TDD) y Vue 3 + TypeScript en un monorepo dockerizado.

## Instrucciones de ejecución:

1. Construiremos las imágenes antes de levantar los servicios, incluso si ya existen, y luego ejecutaremos `up` normalmente,  
   dejando libre el terminal para otras tareas.
   ```
   docker compose up --build -d
   ```

2. Ejecutaremos la migración de la base de datos:
   ```
   docker compose exec backend bin/console cache:clear
   docker compose exec backend bin/console doctrine:migrations:diff
   docker compose exec backend bin/console doctrine:migrations:migrate
   ```

3. Accederemos a la aplicación:
    - Frontend: [http://localhost:5173/](http://localhost:5173/)

4. Ejecución de la suite de tests:
   ```
   docker compose exec backend bin/phpunit
   ```

5. **[Opcional]** Limpieza al terminar la ejecución (*si conoces los riesgos*):
   ```
   docker compose down --rmi all -v
   docker system prune -a --volumes
   ```

## Decisiones de arquitectura:

- **Enfoque arquitectónico**:
    - He intentado cubrir todos los requisitos que en cuanto arquitectura que se pedían.
    - He utilizado MariaDB porque es con lo que trabajo habitualmente.
    - Usé Doctrine ORM para la gestión de la base de datos, ya que es una herramienta robusta y ampliamente utilizada en el ecosistema Symfony, aunque no suelo trabajar con ella habitualmente.

- **Contrato de la API entre frontend y backend**:
    - El contrato de la API se definió directamente en los controladores de Symfony. Cada endpoint está claramente especificado en cuanto a método, parámetros y respuesta.
    - No se utilizó Swagger en esta fase.

- **Trade-offs o sacrificios debido al límite de tiempo**:
    - He utilizado en exceso la IA para generar código, lo que ha acelerado el proceso, pero puede haber introducido errores o malas prácticas.

## Posibles mejoras:

- Creación de un despliegue de producción con Docker.
- Testing del frontend con Cypress.
- Mejora de la gestión de errores y excepciones.
- Documentación de la API con OpenAPI/Swagger.
- Añadir nuevas funcionalidades:
    - Identificación básica de usuarios (sesiones), con rol de administrador para desbloquear fechas.
    - Permitir seleccionar “media jornada” para ciertas fechas (tramos de mañana o tarde).
    - Si varios usuarios acceden al mismo tiempo, bloquear durante el proceso de reserva la fecha que se está contratando.

## Notas personales:

He tenido problemas desde el inicio; mi PC con Windows terminó fallando. Tuve que realizar una instalación nueva de Xubuntu.

Perdí mucho tiempo con la infraestructura.  
No suelo trabajar con Docker, ya que utilizo servidores virtuales gestionados por un equipo de sistemas que replican el entorno de producción.

Al menos me siento satisfecho de haber podido entregar un proyecto funcional como MVP, cubriendo los requisitos de la prueba.  
Gracias por la oportunidad y espero que podamos trabajar juntos en el futuro.
