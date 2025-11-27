# 🔒 RA4. AEE: Creación de un Servicio de Autenticación con API REST y JWT

Este proyecto implementa un servicio básico de autenticación utilizando una arquitectura **Cliente-Servidor (API RESTful)**. El objetivo es simular un flujo de autenticación moderno, donde el cliente (HTML/JavaScript) se comunica con el servidor (PHP) para validar credenciales y recibir un **Token de Acceso** (simulación de JWT - JSON Web Token).

El proyecto aborda el **Resultado de Aprendizaje (RA) 4** del Módulo Profesional DWEC, centrándose en el uso de mecanismos de autenticación y el mantenimiento de estado sin sesiones tradicionales (usando `localStorage` y *Tokens*).

---

## ⚙️ Tecnologías Utilizadas

* **Servidor:** PHP (Backend para la lógica de autenticación y generación de Tokens).
* **Cliente:** HTML5, JavaScript (ES6+), CSS (Frontend para el login y la interfaz de bienvenida).
* **Base de Datos:** No se utiliza BBDD real; se simula con un **array de usuarios** en PHP.
* **Mecanismo de Autenticación:** Token de Acceso (simulado mediante `base64_encode`).
* **Almacenamiento de Estado:** `localStorage` en el cliente.

---

## 🚀 Puesta en Marcha del Proyecto

Para ejecutar esta aplicación, necesitas un entorno de servidor web compatible con PHP, como el entorno **LAMP** (Linux, Apache, MySQL, PHP) configurado en la Práctica RA1.

### 1. Preparación del Entorno

1.  **Asume un Entorno LAMP Funcional:** Asegúrate de que Apache y PHP estén instalados y configurados en tu máquina virtual.
2.  **Ubicación de Archivos:** Copia todos los archivos del proyecto (los cuatro HTML, el `api.php` y el `styles.css`) dentro del directorio raíz de tu servidor web (típicamente `/var/www/html/` o un subdirectorio).
    * Ejemplo de URL de acceso: `http://localhost/Creacion-de-un-Servicio-de-Autenticacion-con-ApiRest-/index.html`

### 2. Archivos del Proyecto

El proyecto consta de los siguientes archivos clave:

| Archivo | Tipo | Función |
| :--- | :--- | :--- |
| `api.php` | Servidor (PHP) | Contiene la lógica de la API REST. Maneja las rutas `/api/login` y `/api/welcome`. Genera y verifica el token. |
| `index.html` | Cliente (HTML/JS) | Pantalla de **Login**. Maneja el envío de credenciales por `fetch` y el almacenamiento del Token. |
| `welcome.html` | Cliente (HTML/JS) | Pantalla **Protegida** de Bienvenida. Envía el Token en la cabecera `Authorization` para acceder a los datos. Incluye el botón *Cerrar Sesión*. |
| `no-permisos.html` | Cliente (HTML) | Pantalla de error para las redirecciones 403 (Acceso Denegado). |
| `styles.css` | Cliente (CSS) | Estilos básicos para las páginas HTML. |

---

## 🔑 Endpoints de la API (Servidor)

La API simula dos *endpoints* RESTful:

| Método | Endpoint | Descripción | Requisito de Seguridad |
| :--- | :--- | :--- | :--- |
| **POST** | `/api/login` | Recibe `username` y `password`. Si son válidos, devuelve el Token de acceso. | Ninguno |
| **GET** | `/api/welcome` | Endpoint **protegido**. Si el Token en la cabecera `Authorization` es válido, devuelve los datos del usuario y la hora actual. | Token Válido |

### Credenciales de Prueba (Array de PHP)

Utiliza cualquiera de estas combinaciones para iniciar sesión:

| Usuario | Contraseña |
| :--- | :--- |
| `admin` | `1234` |
| `user` | `abcd` |

---

## 💡 Flujo de Trabajo (Cliente - Servidor)

1.  **Inicio (Login):** El usuario introduce las credenciales en `index.html`.
2.  **Petición:** JavaScript envía las credenciales mediante **POST** a `/api/login`.
3.  **Respuesta (Éxito):** La API (PHP) valida las credenciales, genera un Token (`base64`), y lo devuelve al cliente (Código **200 OK**).
4.  **Almacenamiento:** JavaScript guarda el Token en `localStorage` y redirige a `welcome.html`.
5.  **Acceso Protegido:** Al cargar `welcome.html`, JavaScript recupera el Token de `localStorage` y lo adjunta a la cabecera `Authorization: Bearer <token>` para hacer una petición **GET** a `/api/welcome`.
6.  **Verificación:** La API (PHP) verifica que el Token sea válido.
    * Si es válido: Devuelve datos (Código **200 OK**).
    * Si es inválido: Devuelve un código **403 Forbidden** y el cliente redirige a `no-permisos.html`.
7.  **Cerrar Sesión:** Al hacer clic en el botón *Cerrar Sesión*, JavaScript elimina el Token de `localStorage` y redirige al `index.html`.
