# SCMIPS Admin Panel --- Learning & Implementation Plan

## 1. Project Overview

Build a secure, maintainable admin panel for the SCMIPS website. College
staff should be able to manage academic-year gallery sessions and
photographs without editing website code.

This is a **learn-by-building project**: use Cursor as a coding
assistant, while understanding the architecture, reviewing generated
code, testing features, and learning the Python/backend concepts behind
each implementation.

### Primary goals

-   Build a reliable gallery management system.
-   Preserve the existing public SCMIPS website and its design.
-   Allow authorized staff to manage gallery sessions and images.
-   Integrate the admin backend with the existing public gallery.
-   Learn practical Python backend engineering skills.

### Scope boundary

Start with gallery management. Keep the architecture modular so that
other website content-management features can be added later without
rebuilding the application.

------------------------------------------------------------------------

## 2. Technology Stack

  -----------------------------------------------------------------------
  Layer                   Technology              Purpose
  ----------------------- ----------------------- -----------------------
  Programming language    Python 3.12+            Application language

  Backend framework       FastAPI                 HTTP API and
                                                  application logic

  Database                PostgreSQL              Persistent application
                                                  data

  ORM                     SQLAlchemy 2.x          Database models and
                                                  queries

  Validation              Pydantic                Request and response
                                                  validation

  Database migrations     Alembic                 Version-controlled
                                                  schema changes

  Frontend                HTML, CSS, JavaScript   Admin interface

  UI framework            Bootstrap 5             Responsive components
                                                  and layout

  Templates (if needed)   Jinja2                  Server-rendered HTML

  Image processing        Pillow                  Image decoding,
                                                  resizing and thumbnails

  Testing                 Pytest                  Unit and integration
                                                  tests

  Code quality            Ruff                    Linting and formatting

  Containerization        Docker                  Consistent development
                                                  and deployment

  Web server / ASGI       Uvicorn                 Run the FastAPI
                                                  application

  Reverse proxy           Nginx                   HTTPS termination and
                                                  request routing

  Image storage           Cloudflare R2 or Amazon Durable image storage
                          S3                      

  Version control         Git and GitHub          Source control and
                                                  collaboration

  CI/CD                   GitHub Actions          Automated checks and
                                                  deployment workflow

  Optional caching/jobs   Redis; task queue when  Caching and background
                          needed                  processing
  -----------------------------------------------------------------------

### Stack decisions

-   Use PostgreSQL for a new deployment unless the college's existing
    infrastructure strongly favors another supported database.
-   Use SQLAlchemy 2.x with Alembic migrations.
-   Start with local image storage during development, but define a
    storage-service interface so production can use R2 or S3.
-   Do not add Redis, Celery, RabbitMQ, or microservices until a real
    requirement justifies them.
-   FastAPI does not provide a Django-style built-in admin interface;
    the custom dashboard is part of this project.

------------------------------------------------------------------------

## 3. Learn-by-Building Workflow

For every module, follow this cycle:

1.  **Learn:** Understand the relevant concept in simple language.
2.  **Plan:** Identify the feature, data flow, files and acceptance
    criteria.
3.  **Prompt Cursor:** Give Cursor a focused task with explicit
    boundaries.
4.  **Review:** Read the generated code and understand important
    functions, classes and dependencies.
5.  **Run:** Execute the application locally.
6.  **Test:** Test expected behavior, invalid input and failure cases.
7.  **Explain:** Be able to explain the implementation without relying
    on Cursor.
8.  **Checkpoint:** Complete a short exercise or knowledge check before
    moving on.

### Cursor usage rules

-   Ask Cursor to inspect the existing repository before changing files.
-   Work in small, reviewable tasks rather than asking it to build the
    entire application at once.
-   Ask it to explain unfamiliar code and design choices.
-   Do not accept unexplained dependencies or unnecessary abstractions.
-   Never paste production secrets, passwords, API keys or hosting
    credentials into prompts or source control.
-   Review every database migration before applying it.
-   Run tests and inspect the Git diff after each meaningful change.
-   Commit working milestones to Git.

------------------------------------------------------------------------

## 4. Phase 0 --- Discovery and Requirements

**Estimated duration:** 1--2 days

### Tasks

-   Inspect the current SCMIPS gallery and public website.
-   Determine whether the current gallery is static HTML or generated
    dynamically.
-   Inspect available source code and understand how current images are
    stored and referenced.
-   Confirm who controls the domain, DNS and hosting.
-   Identify the current hosting provider and whether it supports
    Python/ASGI applications.
-   Confirm whether the existing website can be modified.
-   Determine whether a subdomain such as `admin.scmips.in` can be
    configured.
-   Confirm expected image formats, maximum file size and approximate
    gallery volume.
-   Identify the staff members and roles that will use the panel.
-   Decide how existing gallery content will be migrated or preserved.

### Concepts to learn

-   Client-server architecture
-   Frontend vs backend
-   HTTP request/response lifecycle
-   Static vs dynamic websites
-   REST API basics
-   DNS, domains and hosting fundamentals

### Deliverables

-   Requirements document
-   Feature list and user roles
-   Existing-site integration plan
-   Hosting and deployment decision
-   Initial data model sketch

### Acceptance criteria

Do not begin implementation until the team understands how the existing
public gallery can receive updated content and where the new application
can run.

------------------------------------------------------------------------

## 5. Phase 1 --- Python Fundamentals

**Estimated duration:** 5--7 days, adjusted to current knowledge

### Topics

-   Variables and built-in data types
-   Lists, tuples, sets and dictionaries
-   Conditions and loops
-   Functions, parameters and return values
-   Scope and reusable modules
-   Object-oriented programming: classes, objects, inheritance and
    composition
-   Exception handling
-   File handling and JSON
-   Imports and packages
-   Virtual environments and pip
-   Type hints
-   Basic asynchronous programming and `async`/`await`

### Practical exercises

-   Write a small student-management program.
-   Create functions to add, update and delete gallery records in
    memory.
-   Model an `AcademicSession` and `GalleryImage` using Python classes.
-   Read and write JSON data.
-   Validate a filename and handle invalid input.
-   Create and activate a virtual environment.

### Deliverable

A small Python practice project and confidence with the language
features needed for FastAPI.

------------------------------------------------------------------------

## 6. Phase 2 --- HTTP, REST and FastAPI

**Estimated duration:** 5--7 days

### Topics

-   What an API is and why applications use APIs
-   HTTP methods: GET, POST, PUT, PATCH and DELETE
-   Status codes and error responses
-   Headers, query parameters and path parameters
-   JSON request and response bodies
-   REST resource design
-   FastAPI path operations
-   Pydantic request and response schemas
-   Dependency injection
-   OpenAPI and Swagger documentation
-   Basic async concepts

### Initial endpoints

``` text
GET    /api/v1/health
GET    /api/v1/sessions
POST   /api/v1/sessions
GET    /api/v1/gallery
```

Add update, delete and image endpoints only as their corresponding
features are implemented.

### Exercises

-   Build a small in-memory API before connecting a database.
-   Test endpoints through Swagger UI or an API client.
-   Return appropriate status codes for success and errors.
-   Validate missing and malformed input.

### Deliverable

A working FastAPI application with documented, validated endpoints.

------------------------------------------------------------------------

## 7. Phase 3 --- PostgreSQL, SQLAlchemy and Alembic

**Estimated duration:** 7--10 days

### Topics

-   Relational database concepts
-   Tables, rows, columns and data types
-   Primary keys and foreign keys
-   One-to-many relationships
-   Constraints and indexes
-   SELECT, INSERT, UPDATE and DELETE
-   JOIN operations
-   Transactions
-   SQLAlchemy 2.x models and queries
-   Database sessions and dependency management
-   Alembic migrations
-   Basic query performance and pagination

### Initial data model

#### Admin

Suggested fields:

-   `id`
-   `name`
-   `email` (unique)
-   `password_hash`
-   `role`
-   `is_active`
-   `created_at`
-   `updated_at`

#### AcademicSession

Suggested fields:

-   `id`
-   `title`
-   `slug` (unique)
-   `is_published`
-   `sort_order`
-   `created_at`
-   `updated_at`

#### GalleryImage

Suggested fields:

-   `id`
-   `session_id` (foreign key)
-   `title` (optional)
-   `image_url` or storage key
-   `thumbnail_url` or thumbnail storage key
-   `alt_text`
-   `sort_order`
-   `is_published`
-   `uploaded_by` (admin foreign key)
-   `created_at`
-   `updated_at`

Finalize fields after discovery. Avoid storing image binary data
directly in database rows.

### Deliverables

-   PostgreSQL database
-   SQLAlchemy models and relationships
-   Alembic migration setup
-   Database connection and session handling
-   Tested CRUD operations

### Acceptance criteria

A session can have multiple images; database constraints prevent invalid
relationships; migrations can create and update the schema reliably.

------------------------------------------------------------------------

## 8. Phase 4 --- Authentication and Application Security

**Estimated duration:** 5--7 days

### Topics

-   Authentication vs authorization
-   Password hashing and verification
-   Secure browser authentication
-   Cookie-based sessions vs token-based authentication
-   Role-based access control
-   CSRF protection for cookie-authenticated forms
-   CORS and trusted origins
-   Rate limiting
-   Environment variables and secret management
-   Secure file-upload validation
-   Security headers and HTTPS

### Features

-   Admin login and logout
-   Protected dashboard and management routes
-   Admin roles if multiple staff members need different permissions
-   Password change
-   Login attempt restrictions
-   Secure image upload validation
-   Confirmation for destructive actions

### Security requirements

-   Never store plaintext passwords.
-   Never commit `.env` files or secrets.
-   Validate uploaded file size, extension, MIME type and actual image
    decoding.
-   Generate server-side unique filenames or object keys.
-   Prevent path traversal and executable uploads.
-   Restrict upload access and serve images safely.
-   Apply authorization checks on every protected operation, not just by
    hiding UI controls.
-   Use HTTPS in production.
-   Configure trusted hosts/origins and secure cookies appropriately.
-   Back up the database and image storage.

### Deliverable

A protected admin area with tested authentication and authorization.

------------------------------------------------------------------------

## 9. Phase 5 --- Admin Frontend and Dashboard

**Estimated duration:** 7--10 days

### UI technology

-   HTML
-   CSS
-   Bootstrap 5
-   Vanilla JavaScript
-   Jinja2 only if server-rendered templates are selected

### Pages

1.  Login
2.  Dashboard
3.  Gallery management
4.  Upload images
5.  Academic session management
6.  Admin profile
7.  Settings

### Dashboard features

-   Total gallery images
-   Total academic sessions
-   Recently uploaded images
-   Published and unpublished counts
-   Quick upload action

### Gallery features

-   Image grid and previews
-   Search and filtering by academic session
-   Bulk image upload
-   Edit image title and alt text
-   Publish/unpublish
-   Delete with confirmation
-   Reorder images
-   Pagination or lazy loading when required

### UX requirements

-   Professional design aligned with SCMIPS branding
-   Responsive on desktop, tablet and mobile
-   Clear loading, success and error states
-   Accessible labels and keyboard-friendly controls
-   No unnecessary visual effects or clutter

### Deliverable

A responsive custom admin dashboard connected to the backend.

------------------------------------------------------------------------

## 10. Phase 6 --- Gallery Management and Public Website Integration

**Estimated duration:** 4--7 days

### Admin workflow

1.  Admin signs in.
2.  Admin creates or selects an academic session.
3.  Admin uploads one or more photographs.
4.  Backend validates and processes the images.
5.  Image metadata is stored in the database.
6.  Admin previews and publishes the images.
7.  Public gallery displays published content.

### Public API examples

``` text
GET /api/v1/public/sessions
GET /api/v1/public/sessions/{slug}/images
```

Public endpoints should return only published sessions and published
images.

### Integration tasks

-   Preserve existing gallery content during migration.
-   Modify the existing public gallery only where necessary.
-   Fetch session and image data from the API.
-   Keep the public website's existing visual design.
-   Handle API failures gracefully.
-   Add pagination or lazy loading if gallery volume requires it.
-   Test desktop and mobile layouts.
-   Verify that unpublished or deleted content is not displayed
    publicly.

### Deliverable

A staff member can publish an image through the admin panel and see it
appear on the existing public gallery without editing HTML manually.

------------------------------------------------------------------------

## 11. Phase 7 --- Testing and Advanced Backend Engineering

**Estimated duration:** 5--7 days initially; continue throughout
development

### Testing

Use Pytest for:

-   Unit tests for business logic
-   API endpoint tests
-   Authentication and authorization tests
-   Database integration tests
-   Upload validation tests
-   Public-gallery visibility tests
-   Error and edge-case tests

### Additional engineering topics

  -----------------------------------------------------------------------
  Technology              Purpose                 When to introduce
  ----------------------- ----------------------- -----------------------
  Redis                   Caching and shared      When a real caching
                          temporary data          need exists

  Background processing   Slow image processing   When synchronous
                          or other long-running   processing becomes
                          work                    unsuitable

  Celery or another task  Distributed background  Only if job
  queue                   jobs                    requirements justify it

  Ruff                    Linting and formatting  Early in development

  Structured logging      Production debugging    During implementation

  Monitoring              Error and availability  Before production
                          visibility              
  -----------------------------------------------------------------------

Do not introduce Redis, Celery or RabbitMQ simply to make the project
look more advanced.

### Deliverable

Automated tests, consistent code quality checks and useful application
logs.

------------------------------------------------------------------------

## 12. Phase 8 --- Docker, Deployment and Production

**Estimated duration:** 4--7 days

### Topics

-   Linux command-line basics
-   Docker images and containers
-   Docker Compose for local development
-   Environment-specific configuration
-   Uvicorn and production ASGI serving
-   Nginx reverse proxy
-   DNS and subdomain configuration
-   HTTPS certificates
-   Database backups and restoration
-   Deployment logs and monitoring
-   GitHub Actions CI/CD

### Possible production architecture

``` text
Public website
    |
    +-- scmips.in
    |      Existing website hosting
    |
    +-- admin.scmips.in
           |
           Nginx / HTTPS
           |
           FastAPI application
           |
           +-- PostgreSQL
           |
           +-- Cloudflare R2 / Amazon S3
```

This is a proposed architecture, not a confirmed hosting arrangement.
Final deployment depends on the existing provider, DNS access, available
infrastructure and the college's requirements.

### Deployment checklist

-   Confirm hosting supports the selected Python deployment model.
-   Configure production environment variables.
-   Use a production database, not a local development database.
-   Configure durable image storage.
-   Configure HTTPS and domain routing.
-   Apply database migrations safely.
-   Create and test backups.
-   Configure logs and monitoring.
-   Run smoke tests after deployment.
-   Verify public gallery integration.
-   Document recovery and rollback steps.

### Deliverable

A deployed admin panel integrated with the SCMIPS website.

------------------------------------------------------------------------

## 13. Approximate Timeline

  Phase                           Estimated duration
  ----------------------------- --------------------
  Discovery and requirements               1--2 days
  Python fundamentals                      5--7 days
  HTTP and FastAPI                         5--7 days
  PostgreSQL and SQLAlchemy               7--10 days
  Authentication and security              5--7 days
  Frontend and dashboard                  7--10 days
  Gallery integration                      4--7 days
  Testing and advanced topics              5--7 days
  Deployment                               4--7 days

**Total estimate:** Approximately 43--57 working days, depending on
existing knowledge, daily availability, hosting access and feedback from
the college. Learning and implementation may overlap.

------------------------------------------------------------------------

## 14. Definition of Done

The project is ready for college use when:

-   Authorized staff can securely log in and log out.
-   Admin permissions are enforced on the backend.
-   Staff can create, edit and publish academic sessions.
-   Staff can upload, preview, reorder and delete gallery images.
-   Upload validation and storage are reliable.
-   Public users can see only published content.
-   The existing website design and content are preserved.
-   The application works on mobile and desktop.
-   Database migrations and backups are documented and tested.
-   Automated tests cover important flows.
-   Production uses HTTPS and secure configuration.
-   Deployment and recovery instructions are documented.

------------------------------------------------------------------------

## 15. Learning Checkpoints

At the end of each major phase, be able to answer these questions
without relying on Cursor:

-   **Python:** Can I explain functions, classes, exceptions, modules
    and type hints?
-   **FastAPI:** Can I create an endpoint, validate input and return the
    correct status code?
-   **Database:** Can I explain primary keys, foreign keys,
    relationships, joins and migrations?
-   **Security:** Can I explain password hashing, authentication,
    authorization and secure uploads?
-   **Testing:** Can I write and run a test for an API endpoint?
-   **Deployment:** Can I explain how a domain request reaches the
    FastAPI application and database?

The project is not complete from a learning perspective until you can
explain the important decisions and debug common issues yourself.

------------------------------------------------------------------------

## 16. First Steps

Do not start by asking Cursor to generate the entire application.

Begin in this order:

1.  Inspect the existing SCMIPS website, gallery and available source
    code.
2.  Confirm the hosting provider and access to DNS/domain configuration.
3.  Finalize the public-gallery integration approach.
4.  Write the requirements and acceptance criteria.
5.  Set up Python, Git and a virtual environment.
6.  Learn the Python concepts needed for the first implementation.
7.  Build a minimal FastAPI health-check endpoint.
8.  Continue phase by phase using the learn-by-building workflow.

**Guiding principle:** Cursor helps with implementation; you remain
responsible for understanding, reviewing, testing and maintaining the
application.
