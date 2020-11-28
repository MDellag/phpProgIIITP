URL base del backend:  https://tp-php-progiii.herokuapp.com


EPs:
 /users/register POST   ---> body (json) username: string, password:string, mail: string, type_user: number 
 /users/login POST   ---> body (json) username: string, password:string     

 /empleados/   GET --->  Obtiene a los Empleados ACtivos (Sin DropDate)
 /empleados/all GET   --->  Obtiene a los Empleados (Todos)
 /empleados/ingreso GET   ---> Obtiene todos los Login que hubo solo type User
 /empleados/ingresoDate/{date}  GET   --->  Obtiene todos los Login que hubo solo type User Por Fecha
 /empleados/ingresoDates/{date1}/{date2}  GET  --->  Obtiene todos los Login que hubo solo type User ENTRE 2 Fechas
 /empleados/dni   GET --->  Obtiene un empleado que coincida con el DNI
 /empleados/operaciones/{idSector}  GET    --->  Obtiene las operaciones realizadas segun el sector especificado (sector cocina, barra, choperas, candy_bar)
 /empleados/operaciones/{idSector}/empleados/{idEmpleado}  GET  --->  Obtiene las operaciones realizadas segun el sector especificado (sector cocina, barra, choperas, candy_bar) segun el Empleado
 /empleados/{idEmpl}/operaciones/{idSector}/    GET  ---> Obtiene las operaciones realizadas segun el sector especificado (sector cocina, barra, choperas, candy_bar) segun el Empleado
 /empleados/{idEmpl}/operaciones/   GET --->  Obtiene las operaciones realizadas segun el segun el Empleado
 /empleados/    POST --->  body(Json) name:string,  lastname:string, id_puesto:number, dni:number     Agrega un nuevo empleado
 /empleados/{dni}    PUT --->  body(Json) name:string,  lastname:string, id_puesto:number     Actualiza al Empleado
 /empleados/{dni}   DELETE --->  da de baja al empleado solo Logicamente, no Fisica


 /pedidos/masvendido/ GET  --->  obtiene la Comida del Menu mas Vendida
 /pedidos/menosvendido/  GET --->  obtiene la Comida del Menu menos Vendida
 /pedidos/status/{status}  GET --->  obtiene los pedidos segun el status indicado (solo entregado, tarde, solicitado)
 /pedidos/all GET  --->  obtiene los pedidos 
 /pedidos/{CODE} GET  --->  obtiene el pedido por CODE SOlo USER
 /pedidos/  POST  --->  body(json)  orden: array number(cada numero del array corresponde a un ID del producto que se puede obtener del MENU), id_mesa: number 
 /pedidos/{code}  PUT  --->  body(json)  status:string    modifica el estado del pedido SOLO PARA ADMIN 
 /pedidos/cancelar/{code}  DELETE  --->  Cancela el Pedido.. solo USER ya que es el que "ORDENO" el pedido



 /mesas/usoMesas  GET  --->  Obtiene el uso de las mesas.. la que mas se uso y la que menos se uso;  solo ADMIN
 /mesas/facturacion  GET  --->  Obtiene la facturacion de las mesas
 /mesas/facturacion/total  GET  --->  Obtiene la facturacion total de las mesas
 /mesas/facturacion/total/fechas/{dateStart}/{dateEnd}  GET  --->  Obtiene la facturacion total de las mesas entre 2 fechas

 /menu[/] GET ---> Lista el MENU del Restaurante (No usuario para realizar esta accion)