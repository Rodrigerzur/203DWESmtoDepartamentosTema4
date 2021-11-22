<?php
if (isset($_REQUEST['volver'])) {//si se pulsa el botÃ³n de volver
    header('Location:' . '../../../../proyectoTema4/indexProyectoTema4.php'); //Link al indexProyectoTema4
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mto Departamentos Tema 4</title>
        <link href="../webroot/css/style.css" rel="stylesheet"> 
    </head>
    <body>
        <header>
            <div class="titulo">Mantenimiento de Departamentos</div>
        </header>

        <main class="mainIndex">
            <div class="contenidoIndex">
                <nav>
                    <ul class="enlaces">
                        <li><a href="exportarDepartamentos.php">EXPORTAR </a></li>
                        <li><a href="importarDepartamentos.php">IMPORTAR </a></li>
                        <li><a href="crearDepartamento.php">AÑADIR </a></li>
                    </ul>
                </nav> 
                <?php
                //Incluir la libreria de funciones para la validacion
                require_once '../core/210322ValidacionFormularios.php';
                require_once '../config/confDBPDO.php'; //Archivo con configuracion de PDO

                define("OBLIGATORIO", 1);
                define("OPCIONAL", 0);
                $entradaOK = true;

                $error = null;
                ?>
                <div class="formBuscar">
                    <form name="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="formBuscar">
                        <label for="DescDepartamento" class="descDepartamento">Descripción de departamento: </label>
                        <input type="text" style="background-color: white" id="DescDepartamento" name="DescDepartamento" value="<?php echo(isset($_REQUEST['DescDepartamento']) ? $_REQUEST['DescDepartamento'] : null); ?>" class="descDepartamento">
                        <?php echo($error != null ? "<span style='color:red'>" . $error . "</span>" : null); ?>
                        <input type="submit" value="Buscar" name="buscar" class="enviar">
                    </form>
                </div>
                <div class="resultadoConsulta">
                    <?php
                    if (isset($_REQUEST['buscar'])) {// Comprobamos si se ha enviado el formulario
                        $error = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescDepartamento'], 255, 1, OPCIONAL);
                        if ($error != null) {//Si hay errores
                            $entradaOK = false;
                            $_REQUEST['DescDepartamento'] = "";
                        }
                    } else {//Si el usuario no ha enviado el formulario
                        $_REQUEST['DescDepartamento'] = "";
                    }
                    if ($entradaOK) {//Si el usuario ha rellenado correctamente el formulario
                        try {
                            /* Establecemos la connection con pdo */
                            $miDB = new PDO(HOST, USER, PASSWORD);
                            /* configurar las excepcion */
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $sql = 'SELECT * FROM Departamento WHERE DescDepartamento LIKE "%":DescDepartamento"%" ';

                            $consulta = $miDB->prepare($sql); //Preparamos la consulta
                            $parametros = [":DescDepartamento" => $_REQUEST['DescDepartamento']];
                            $consulta->execute($parametros); //ejecutamos la consulta
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
                                        if ($consulta->rowCount() > 0) {//Si hay algÃºn resultado
                                            $registro = $consulta->fetchObject(); //Obtenemos la primera fila del resultado de la consulta y avanzamos el puntero a la siguiente fila
                                            while ($registro) { //Mientras haya un registro  
                                                ?>
                                                <tr>
                                                    <td class="campo" style="<?php echo($registro->FechaBaja ? 'color: red' : 'color: green'); ?>"><?php echo $registro->CodDepartamento ?></td>
                                                    <td class="campo" style="<?php echo($registro->FechaBaja ? 'color: red' : 'color: green'); ?>"><?php echo $registro->DescDepartamento ?></td>
                                                    <td class="campo" style="<?php echo($registro->FechaBaja ? 'color: red' : 'color: green'); ?>" class="fecha"><?php echo($registro->FechaBaja ? $registro->FechaBaja : 'null'); ?></td>
                                                    <td class="campo" style="<?php echo($registro->FechaBaja ? 'color: red' : 'color: green'); ?>"><?php echo $registro->VolumenNegocio ?></td>

                                                    <td class="boton"><button name='editar' value="Editar" style="background-color: transparent; border: 0;" ><a href="<?php echo 'editarDepartamento.php?CodDepartamentoEnCurso=' . $registro->CodDepartamento ?>">EDITAR</a></button></td>       
                                                    <td class="boton"><button name='consultar' value="Consultar" style="background-color: transparent; border: 0;"><a href="<?php echo 'mostrarDepartamento.php?codigo=' . $registro->CodDepartamento ?>">CONSULTAR</a></button></td>
                                                    <td class="boton"><button name='borrar' value="Borrar" style="background-color: transparent; border: 0;"><a href="<?php echo 'bajaDepartamento.php?codigo=' . $registro->CodDepartamento ?>">BORRAR</a></button></td>                                      
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

                            </div>
                            <?php
                        } catch (PDOException $excepcion) {
                            $errorExcepcion = $excepcion->getCode();
                            $mensajeExcepcion = $excepcion->getMessage();

                            echo "<span style='color: red;'>Error: </span>" . $mensajeExcepcion . "<br>"; //Mostramos el mensaje de la excepción
                            echo "<span style='color: red;'>Código del error: </span>" . $errorExcepcion; //Mostramos el código de la excepción
                        } finally {
                            unset($miDB); //Cerramos la conexión 
                        }
                    }
                    ?>
                </div>
                <form  name="formularioconsulta" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <table class="botones">
                        <tr>
                            <td>
                                <button type="submit" name='volver' value="Volver" class="volver">SALIR</button>
                            </td>
                        </tr>
                    </table> 
                </form>

            </div>
        </main>
    </body>
</html>