<?php
//require_once "../config/confLocation.php";//Incluimos el archivo de configuración para poder acceder a la constante de la url del header Location   
if (isset($_REQUEST['volver'])) {//si pulsa el botón de volver
    header('Location:' . URL . '/proyectoDWES/indexProyectoDWES.php'); //Redirigimos al usuario a la página inicial de DWES
    exit();
}

if (isset($_REQUEST['mostrarCodigo'])) {//Si pulsa el botón de mostrar codigo
    header('Location:' . URL . '/proyectoMtoDepartamentosTema4/codigoPHP/mostrarCodigo.php'); //Redirigimos al usuario a la página mostrarCodigo.php
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mto Departamentos</title>
        <link href="../webroot/css/style.css" rel="stylesheet"> 
    </head>
    <body>
        <header>
            <div class="logo">Mantenimiento de Departamentos</div>
        </header>

        <main class="mainIndex">
            <div class="contenidoIndex">
                <nav>
                    <ul class="enlaces">
                        <li><a href="exportarDepartamentos.php">EXPORTAR </a></li>
                        <li><a href="importarDepartamentos.php">IMPORTAR </a></li>
                        <li><a href="altaDepartamento.php">AÑADIR </a></li>
                    </ul>
                </nav> 
                <?php
                require_once "../core/210322ValidacionFormularios.php"; //Incluimos la librería de validación para comprobar los campos del formulario
                require_once "../config/confDBPDO.php"; //Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 

                define("OBLIGATORIO", 1);
                define("OPCIONAL", 0);
                $entradaOK = true;

                $error = null; //Inicializamos a null la variable donde almacenaremos los errores del campo
                ?>
                <div class="formBuscar">
                    <form name="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="formBuscar">
                        <label for="DescDepartamento" class="descDepartamento">Descripción de departamento: </label>
                        <input type="text" style="background-color: #D2D2D2" id="DescDepartamento" style="background-color: #D2D2D2" name="DescDepartamento" value="<?php echo(isset($_REQUEST['DescDepartamento']) ? $_REQUEST['DescDepartamento'] : null); ?>" class="descDepartamento">
                        <?php echo($error != null ? "<span style='color:red'>" . $error . "</span>" : null); ?>
                        <input type="submit" value="Buscar" name="buscar" class="enviar">
                    </form>
                </div>
                <div class="resultadoConsulta">
                    <?php
                    if (isset($_REQUEST['buscar'])) {// Comprobamos si el usuario ha enviado el formulario
                        $error = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescDepartamento'], 255, 1, OPCIONAL); //Comprobamos que la descripción sea alfanumerico

                        if ($error != null) {//Si hay errores
                            $entradaOK = false;
                            $_REQUEST['DescDepartamento'] = "";
                        }
                    } else {//Si el usuario no ha enviado el formulario
                        $_REQUEST['DescDepartamento'] = "";
                    }
                    if ($entradaOK) {//Si el usuario ha rellenado correctamente el formulario
                        try {
                            $miDB = new PDO(HOST, USER, PASSWORD); //Instanciamos un objeto PDO y establecemos la conexión
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Configuramos las excepciones

                            $sql = 'SELECT * FROM Departamento WHERE DescDepartamento LIKE "%":DescDepartamento"%" LIMIT ' . (($numPagina - 1) * MAXDEPARTAMENTOS) . ',' . MAXDEPARTAMENTOS;

                            $consulta = $miDB->prepare($sql); //Preparamos la consulta
                            $parametros = [":DescDepartamento" => $_REQUEST['DescDepartamento']];
                            $consulta->execute($parametros); //Pasamos los parametros y ejecutamos la consulta
                            ?>
                            <div class="tabla">
                                <table class="tablaConsultaCampos">
                                    <thead>
                                        <tr>
                                            <th class="cDepartamento">Código</th>
                                            <th class="dDepartamento">Descripción</th>
                                            <th class="fDepartamento">FechaBaja</th>
                                            <th class="vDepartamento">VolumenNegocio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($consulta->rowCount() > 0) {//Si hay algún resultado
                                            $registro = $consulta->fetchObject(); //Obtenemos la primera fila del resultado de la consulta y avanzamos el puntero a la siguiente fila
                                            while ($registro) { //Mientras haya un registro  
                                                ?>
                                                <tr>
                                                    <td class="campo" style="<?php echo($registro->FechaBaja ? 'color: red' : 'color: green'); ?>"><?php echo $registro->CodDepartamento ?></td>
                                                    <td class="campo" style="<?php echo($registro->FechaBaja ? 'color: red' : 'color: green'); ?>"><?php echo $registro->DescDepartamento ?></td>
                                                    <td class="campo" style="<?php echo($registro->FechaBaja ? 'color: red' : 'color: green'); ?>" class="fecha"><?php echo($registro->FechaBaja ? $registro->FechaBaja : 'null'); ?></td>
                                                    <td class="campo" style="<?php echo($registro->FechaBaja ? 'color: red' : 'color: green'); ?>"><?php echo $registro->VolumenNegocio ?></td>

                                                    <td class="boton"><button name='editar' value="Editar" style="background-color: transparent; border: 0;" ><a href="<?php echo 'editarDepartamento.php?codigo=' . $registro->CodDepartamento ?>"><img src="../webroot/media/editar.png" alt="EDITAR" width="30"></a></button></td>       
                                                    <td class="boton"><button name='consultar' value="Consultar" style="background-color: transparent; border: 0;"><a href="<?php echo 'mostrarDepartamento.php?codigo=' . $registro->CodDepartamento ?>"><img src="../webroot/media/ver.png" alt="CONSULTAR" width="30"></a></button></td>
                                                    <td class="boton"><button name='borrar' value="Borrar" style="background-color: transparent; border: 0;"><a href="<?php echo 'bajaDepartamento.php?codigo=' . $registro->CodDepartamento ?>"><img src="../webroot/media/borrar.png" alt="BORRAR" width="30"></a></button></td>
                                                    <td class="boton"><button name='bajaLogica' value="BajaLogica" style="background-color: transparent; border: 0;"><a href="<?php echo 'bajaLogicaDepartamento.php?codigo=' . $registro->CodDepartamento ?>"><img src="../webroot/media/baja.png" alt="BajaLogica" width="30"></a></button></td>
                                                    <td class="boton"><button name='rehabilitar' value="Rehabilitar" style="background-color: transparent; border: 0;"><a href="<?php echo 'rehabilitarDepartamento.php?codigo=' . $registro->CodDepartamento ?>"><img src="../webroot/media/rehabilitar.png" alt="Rehabilitar" width="30"></a></button></td>
                                                </tr> 
                                                <?php
                                                $registro = $consulta->fetchObject(); //Obtenemos la siguiente fila del resultado de la consulta y avanzamos el puntero a la siguiente fila
                                            }
                                            ?>
                                        </tbody>
                                    </table>


                                    <?php
                                } else {
                                    ?>
                                    <tr>
                                        <th rowspan="4" style="color:red;">No se han encontrado registros</th>
                                    </tr>
                                    </tbody>
                                    </table>
                                    <?php
                                }
                                ?>


                                <?php
                            } catch (PDOException $excepcion) { //si se produce alguna excepción
                                $errorExcepcion = $excepcion->getCode(); //Guardar el código del error 
                                $mensajeExcepcion = $excepcion->getMessage(); //Guardar el mensaje de la excepcion

                                echo "<span>Error: </span>" . $mensajeExcepcion . "<br>"; //mensaje de la excepción
                                echo "<span>Código del error: </span>" . $errorExcepcion; //código de la excepción
                            } finally {
                                unset($miDB); //Cerrar la conexion 
                            }
                        }
                        ?>
                    </div>
                    <form  name="formularioconsulta" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <table class="botones">
                            <tr>
                                <td>
                                    <button type="submit" name='volver' value="Volver" class="volver">VOLVER</button>
                                </td>
                                <td>
                                    <button name='mostrarCodigo' value="mostrarCodigo" class="volver">Mostrar Código</button>
                                </td>
                            </tr>
                        </table> 
                    </form>
                </div>
            </div>
        </main>


    </body>
</html>