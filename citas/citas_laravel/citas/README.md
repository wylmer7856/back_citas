# INTRODUCCION

 La siguiente **API** dessarrollada  para el sistema reserva de citas tiene como finalidad el control de las citas de cada paciente.

 ##  Versión actual   
 **v2.0.0** – Versión actual de la API, con modificaciones en seguridad del API 

## URL Base 
La API del **Sistema Reserva De Citas** está disponible en entorno local  con su propia URL base:  

http://127.0.0.1:8000/api/ 

## Autenticacion y Seguridad 
La autenticación de la API se realiza con el uso de **Sanctum**.

- Método: Sanctum (Bearer Token).  

##  Convenciones de nombres 

- **Rutas:** en **camelCase**, representando acciones (`usuarios`, `especialidades`, `pacientes`, `citas`,etc.).  
- **Parámetros:** `{id}` corresponde al identificador único del recurso.  
- **Cuerpo de peticiones (JSON):** en **camelCase**. 


#  Modelos de Datos  

La API maneja los siguientes modelos principales:  

---

##  Usuario  
Representa a cada persona registrada en el sistema (paciente, médico o administrador).  

| Campo       | Tipo     | Descripción                                       |
|-------------|---------|---------------------------------------------------|
| `id`        | Integer | Identificador único del usuario.                   |
| `nombre`    | String  | Nombre del usuario.                               |
| `apellido`  | String  | Apellido del usuario.                             |
| `email`     | String  | Correo electrónico único para inicio de sesión.   |
| `telefono`  | String  | Número de teléfono de contacto.                   |
| `rol`       | String  | Rol del usuario (`ADMIN`, `MEDICO`, `PACIENTE`).  |
| `password`  | String  | Contraseña encriptada del usuario.                |

**Ejemplo (JSON):**  
```json
{
  "id": 1,
  "nombre": "Carlos",
  "apellido": "Ramírez",
  "email": "carlos.ramirez@example.com",
  "telefono": "3004567890",
  "rol": "PACIENTE",
  "password": "$2y$10$eImiTXuWVxfM37uY4JANjQ=="
}
```

#  ENDPOINTS  

En esta sección se detallan los endpoints disponibles en la API, organizados por rol y nivel de acceso.  

---

##  Públicos (sin autenticación)  
| Método | Endpoint       | Descripción                                   |
|--------|----------------|-----------------------------------------------|
| POST   | `/login`       | Inicio de sesión, devuelve token de acceso.   |
| POST   | `/usuarios`    | Registro de un nuevo usuario (rol PACIENTE).  |

---

##  Autenticación  
| Método | Endpoint   | Descripción                                  |
|--------|------------|----------------------------------------------|
| POST   | `/login`   | Inicia sesión y devuelve el token del usuario. |
| POST   | `/logout`  | Cierra sesión y revoca el token.             |
| GET    | `/me`      | Devuelve los datos del usuario autenticado.  |

---

##  Usuario (role: PACIENTE)  
| Método | Endpoint        | Descripción                                   |
|--------|----------------|-----------------------------------------------|
| GET    | `/citas`       | Lista las citas del paciente autenticado.     |
| POST   | `/citas`       | Crea una nueva cita con un médico.            |
| GET    | `/historial`   | Lista su historial médico.                    |

---

##  Médico (role: MEDICO)  
| Método | Endpoint             | Descripción                                |
|--------|----------------------|--------------------------------------------|
| GET    | `/citas`             | Lista las citas asignadas al médico.       |
| GET    | `/historial/{id}`    | Consulta el historial médico de un paciente. |
| POST   | `/historial`         | Registra una nueva entrada en el historial. |

---

##  Administrador (role: ADMIN)  

###  Usuarios  
| Método | Endpoint             | Descripción                       |
|--------|----------------------|-----------------------------------|
| GET    | `/usuarios`          | Lista todos los usuarios.         |
| GET    | `/usuarios/{id}`     | Consulta un usuario por ID.       |
| POST   | `/usuarios`          | Crea un nuevo usuario.            |
| PUT    | `/usuarios/{id}`     | Actualiza un usuario existente.   |
| DELETE | `/usuarios/{id}`     | Elimina un usuario por ID.        |

---

###  Especialidades  
| Método | Endpoint                   | Descripción                       |
|--------|----------------------------|-----------------------------------|
| GET    | `/especialidades`          | Lista todas las especialidades.   |
| POST   | `/especialidades`          | Crea una nueva especialidad.      |
| GET    | `/especialidades/{id}`     | Consulta detalle de una especialidad. |
| PUT    | `/especialidades/{id}`     | Actualiza una especialidad.       |
| DELETE | `/especialidades/{id}`     | Elimina una especialidad por ID.  |

---

###  Médico - Especialidad  
| Método | Endpoint                      | Descripción                        |
|--------|-------------------------------|------------------------------------|
| GET    | `/medico-especialidad`        | Lista todas las relaciones.        |
| POST   | `/medico-especialidad`        | Asigna una especialidad a un médico. |
| DELETE | `/medico-especialidad/{id}`   | Elimina una relación.              |

---

###  Citas  
| Método | Endpoint             | Descripción                       |
|--------|----------------------|-----------------------------------|
| GET    | `/citas`             | Lista todas las citas del sistema.|
| GET    | `/citas/{id}`        | Consulta una cita específica.     |
| PUT    | `/citas/{id}`        | Actualiza una cita.               |
| DELETE | `/citas/{id}`        | Elimina una cita.                 |

---

###  Historial Médico  
| Método | Endpoint               | Descripción                              |
|--------|------------------------|------------------------------------------|
| GET    | `/historial`           | Lista todos los historiales médicos.     |
| GET    | `/historial/{id}`      | Consulta un historial por ID.            |
| POST   | `/historial`           | Crea un historial médico.                |
| PUT    | `/historial/{id}`      | Actualiza un historial médico.           |
| DELETE | `/historial/{id}`      | Elimina un historial médico.             |


## Códigos de Estado

200 OK → Petición exitosa.

201 Created → Recurso creado correctamente.

400 Bad Request → Error en parámetros enviados.

401 Unauthorized → No autenticado o token inválido.

403 Forbidden → Usuario no tiene permisos.

404 Not Found → Recurso no existe.

500 Internal Server Error → Error inesperado en el servidor.

## Contacto y Soporte

Wylmer Andrés Morales

## Conclusión 

Este proyecto se desarrolló con fines académicos para aprender a implementar una API RESTful con Laravel y Sanctum.
