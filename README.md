# 🗳️ VOLEDU - Voto Educativo

![Estado](https://img.shields.io/badge/Estado-Terminado-success?style=for-the-badge)
![Versión](https://img.shields.io/badge/Versión-v1.0-blue?style=for-the-badge)
![PHP](https://img.shields.io/badge/Backend-PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

## 📋 Descripción

Este es un sistema web integral desarrollado para la gestión automatizada de procesos electorales escolares o institucionales. Permite a los electores emitir su voto de manera digital validando su identidad mediante DNI, y ofrece a los administradores un control total sobre candidatos y resultados en tiempo real.

---

## ✨ Características Principales

* **🤖 Generación Automática de Usuarios:** El sistema simplifica el registro creando automáticamente las credenciales de acceso. Toma el **nombre** y **apellido** de la persona para generar un nombre de usuario único sin necesidad de configuración manual.
* **📊 Resultados en Tiempo Real:** El sistema procesa los votos instantáneamente. Apenas se cierra la votación, el conteo y los ganadores se muestran al instante, sin esperas.
* **👤 Validación por DNI:** Seguridad integrada que asegura que solo los electores empadronados puedan acceder y votar una única vez.
* **🛡️ Panel Administrativo:** Gestión completa de candidatos, listas electorales y configuración del sistema.
* **📱 Diseño Intuitivo:** Interfaz amigable y fácil de usar tanto para el votante como para el administrador.

---

## 🚀 Módulos y Acceso (Entorno Local)

Una vez desplegado el proyecto en tu servidor local (XAMPP/WAMP), estas son las rutas de acceso:

### 1. Interfaz de Votación (Electores)
Donde los usuarios ingresan su DNI para ver a los candidatos y votar.
* **URL:** `http://localhost/Sistema_Votacion/votacion/index.php`

### 2. Panel Administrativo (Backend)
Donde se configura la elección y se gestionan los reportes.
* **URL:** `http://localhost/Sistema_Votacion/login/login.php`

---

## 🔑 Credenciales de Acceso

Para ingresar al **Panel Administrativo**, utiliza los siguientes datos por defecto:

| Rol | Usuario | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `aadm` | `admin` |

> ⚠️ **Importante:** Se recomienda cambiar estas credenciales una vez implementado el sistema en un entorno real.

---

## 💻 Tecnologías y Librerías

El proyecto ha sido construido utilizando tecnologías robustas y librerías modernas:

### Backend & Base de Datos
* **PHP:** Lógica del servidor, procesamiento de votos y algoritmo de generación de usuarios.
* **MySQL:** Gestión de base de datos relacional.

### Frontend & UI
* **HTML5 & CSS3:** Estructura y estilos.
* **Bootstrap:** Framework para diseño responsivo y componentes visuales.
* **Feather Icons:** Iconografía ligera y moderna para la interfaz.
* **JavaScript / jQuery:** Interactividad y peticiones asíncronas.

---

## 🛠️ Instalación y Configuración

Sigue estos pasos para ejecutar el proyecto en tu computadora:

1.  **Clonar el Repositorio:**
    Descarga los archivos o clona el repositorio en tu carpeta de servidor (ej. `C:/xampp/htdocs/`).
    ```bash
    git clone [https://github.com/Almejiaag203/VOLEDU-Voto-Educativo.git](https://github.com/Almejiaag203/VOLEDU-Voto-Educativo.git)
    ```

2.  **Base de Datos:**
    * Abre tu gestor (ej. PHPMyAdmin).
    * Crea una nueva base de datos.
    * Importa el archivo `.sql` incluido en la carpeta `database` (o raíz) del proyecto.

3.  **Conexión:**
    * Verifica el archivo de configuración de conexión (usualmente `conexion.php` o `db.php`) y asegúrate de que los datos (host, user, pass, dbname) coincidan con tu configuración local.

4.  **¡Listo!**
    Abre tu navegador e ingresa a las URLs mencionadas arriba.

---

**Desarrollado con fines educativos y profesionales.**
Copyright © 2025 **TechFusion Data**.
