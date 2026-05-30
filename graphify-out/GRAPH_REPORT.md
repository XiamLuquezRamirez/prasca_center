# Graph Report - .  (2026-05-29)

## Corpus Check
- 249 files · ~254,000 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 794 nodes · 939 edges · 204 communities (180 shown, 24 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 63 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Historias Clinicas y Profesionales|Historias Clinicas y Profesionales]]
- [[_COMMUNITY_Gestion de Usuarios|Gestion de Usuarios]]
- [[_COMMUNITY_Dependencias y Autoload|Dependencias y Autoload]]
- [[_COMMUNITY_Modulo Agenda y Citas|Modulo Agenda y Citas]]
- [[_COMMUNITY_Gestion de Pacientes|Gestion de Pacientes]]
- [[_COMMUNITY_Historia Neuropsicologica|Historia Neuropsicologica]]
- [[_COMMUNITY_Administracion y Pagos|Administracion y Pagos]]
- [[_COMMUNITY_Historia Psicologica|Historia Psicologica]]
- [[_COMMUNITY_Catalogo y Servicios|Catalogo y Servicios]]
- [[_COMMUNITY_Modelo Pacientes|Modelo Pacientes]]
- [[_COMMUNITY_Proveedores de Servicio|Proveedores de Servicio]]
- [[_COMMUNITY_Middleware HTTP|Middleware HTTP]]
- [[_COMMUNITY_Catalogos Medicos|Catalogos Medicos]]
- [[_COMMUNITY_Frontend y JS|Frontend y JS]]
- [[_COMMUNITY_Modelos Principales|Modelos Principales]]
- [[_COMMUNITY_Controlador Base|Controlador Base]]
- [[_COMMUNITY_Gestion de Gastos|Gestion de Gastos]]
- [[_COMMUNITY_Pruebas Unitarias|Pruebas Unitarias]]
- [[_COMMUNITY_Config y Herramientas|Config y Herramientas]]
- [[_COMMUNITY_Modulo 20|Modulo 20]]
- [[_COMMUNITY_Modulo 21|Modulo 21]]
- [[_COMMUNITY_Modulo 22|Modulo 22]]
- [[_COMMUNITY_Modulo 23|Modulo 23]]
- [[_COMMUNITY_Modulo 24|Modulo 24]]
- [[_COMMUNITY_Modulo 25|Modulo 25]]
- [[_COMMUNITY_Modulo 26|Modulo 26]]
- [[_COMMUNITY_Modulo 27|Modulo 27]]
- [[_COMMUNITY_Modulo 28|Modulo 28]]
- [[_COMMUNITY_Modulo 29|Modulo 29]]
- [[_COMMUNITY_Modulo 30|Modulo 30]]
- [[_COMMUNITY_Modulo 31|Modulo 31]]
- [[_COMMUNITY_Modulo 32|Modulo 32]]
- [[_COMMUNITY_Modulo 33|Modulo 33]]
- [[_COMMUNITY_Modulo 34|Modulo 34]]
- [[_COMMUNITY_Modulo 35|Modulo 35]]
- [[_COMMUNITY_Modulo 36|Modulo 36]]
- [[_COMMUNITY_Modulo 37|Modulo 37]]
- [[_COMMUNITY_Modulo 38|Modulo 38]]
- [[_COMMUNITY_Modulo 39|Modulo 39]]

## God Nodes (most connected - your core abstractions)
1. `Citas` - 33 edges
2. `CatalogoController` - 32 edges
3. `Request` - 31 edges
4. `Pacientes` - 24 edges
5. `Request` - 23 edges
6. `Usuario` - 23 edges
7. `Request` - 21 edges
8. `UsuariosController` - 20 edges
9. `Request` - 17 edges
10. `CajaController` - 17 edges

## Surprising Connections (you probably didn't know these)
- `StyleCI Configuration` --complements--> `PrascCenter README`  [INFERRED]
  .styleci.yml → README.md
- `Laravel StyleCI Preset` --references--> `Laravel Framework`  [INFERRED]
  .styleci.yml → README.md
- `UsuariosController` --inherits--> `Controller`  [EXTRACTED]
  app/Models/UsuariosController.php → app/Http/Controllers/Controller.php
- `ExampleTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ExampleTest.php → tests/TestCase.php
- `ExampleTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Unit/ExampleTest.php → tests/TestCase.php

## Communities (204 total, 24 thin omitted)

### Community 0 - "Historias Clinicas y Profesionales"
Cohesion: 0.06
Nodes (3): Request, Request, Profesional

### Community 2 - "Gestion de Usuarios"
Cohesion: 0.05
Nodes (41): autoload, psr-4, config, optimize-autoloader, preferred-install, sort-packages, description, extra (+33 more)

### Community 3 - "Dependencias y Autoload"
Cohesion: 0.08
Nodes (4): Request, Request, Usuario, UsuariosController

### Community 7 - "Administracion y Pagos"
Cohesion: 0.11
Nodes (4): Request, Request, Controller, SistemaController

### Community 8 - "Historia Psicologica"
Cohesion: 0.12
Nodes (7): Request, AuthorizesRequests, BaseController, CajaController, Controller, DispatchesJobs, ValidatesRequests

### Community 11 - "Proveedores de Servicio"
Cohesion: 0.17
Nodes (5): Model, CIE10, CUPS, Entidades, Especialidades

### Community 12 - "Middleware HTTP"
Cohesion: 0.13
Nodes (14): devDependencies, axios, laravel-mix, lodash, postcss, private, scripts, dev (+6 more)

### Community 15 - "Modelos Principales"
Cohesion: 0.22
Nodes (5): BaseTestCase, CreatesApplication, ExampleTest, TestCase, ExampleTest

### Community 16 - "Controlador Base"
Cohesion: 0.18
Nodes (3): Middleware, TrimStrings, TrustHosts

### Community 18 - "Pruebas Unitarias"
Cohesion: 0.22
Nodes (10): CSS Code Style (StyleCI), JavaScript Code Style (StyleCI), Laravel Framework, Laravel StyleCI Preset, MIT License, Packagist Package Registry, PHP Code Style (StyleCI), PrascCenter README (+2 more)

### Community 19 - "Config y Herramientas"
Cohesion: 0.28
Nodes (3): Migration, CreateFailedJobsTable, CreateLogsTable

### Community 25 - "Modulo 25"
Cohesion: 0.47
Nodes (4): Authenticatable, HasApiTokens, User, Notifiable

### Community 27 - "Modulo 27"
Cohesion: 0.50
Nodes (3): Kernel, ConsoleKernel, Schedule

### Community 29 - "Modulo 29"
Cohesion: 0.40
Nodes (3): Kernel, HttpKernel, Kernel

### Community 30 - "Modulo 30"
Cohesion: 0.40
Nodes (4): Plantilla.Cabecera, Plantilla.Footer, Plantilla.Head, Plantilla.Menu

## Knowledge Gaps
- **56 isolated node(s):** `name`, `type`, `description`, `keywords`, `license` (+51 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **24 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Controller` connect `Historia Psicologica` to `Historias Clinicas y Profesionales`, `Dependencias y Autoload`, `Modulo Agenda y Citas`, `Gestion de Pacientes`?**
  _High betweenness centrality (0.171) - this node is a cross-community bridge._
- **Why does `CajaController` connect `Historia Psicologica` to `Administracion y Pagos`?**
  _High betweenness centrality (0.134) - this node is a cross-community bridge._
- **Why does `CatalogoController` connect `Historia Neuropsicologica` to `Administracion y Pagos`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **Are the 15 inferred relationships involving `Citas` (e.g. with `.agenda()` and `.CambioEstadocita()`) actually correct?**
  _`Citas` has 15 INFERRED edges - model-reasoned connections that need verification._
- **What connects `name`, `type`, `description` to the rest of the system?**
  _56 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Historias Clinicas y Profesionales` be split into smaller, more focused modules?**
  _Cohesion score 0.05714285714285714 - nodes in this community are weakly interconnected._
- **Should `Middleware y Registro de Acciones` be split into smaller, more focused modules?**
  _Cohesion score 0.03508771929824561 - nodes in this community are weakly interconnected._