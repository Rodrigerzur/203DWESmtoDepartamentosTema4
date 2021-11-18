<?php
    
if(isset($_REQUEST['cancelar'])){//Si pulsa el botón de cancelar
    header('Location: '.'mtoDepartamentos.php');//Redirigimos al usuario a la página inicial
}

   
    //Incluir la libreria de funciones para la validacion
                require_once '../core/210322ValidacionFormularios.php';
                require_once '../config/confDBPDO.php'; //Archivo con configuracion de PDO

try {
       /* Establecemos la connection con pdo */
                            $miDB = new PDO(HOST, USER, PASSWORD);
                            /* configurar las excepcion */
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "Select DescDepartamento, FechaBaja, VolumenNegocio from Departamento where CodDepartamento=:CodDepartamento";
        $consultaCampos = $miDB->prepare($sql);//Preparacion de la consulta
        $parametrosCampos = [":CodDepartamento" => $_REQUEST['CodDepartamentoEnCurso']];

        $consultaCampos->execute($parametrosCampos);//Almacenar los parametros en la consulta
        $resultadoCampos = $consultaCampos->fetchObject();//Primer resultado

        $descripcionDepartamento=$resultadoCampos->DescDepartamento;
        $fechaBaja=$resultadoCampos->FechaBaja;
        if($fechaBaja==null){//Si el campo está vacío
            $fechaBaja='null';
        }
        $volumenNegocio=$resultadoCampos->VolumenNegocio;

    }catch (PDOException $excepcion){
            $errorExcepcion = $excepcion->getCode();
            $mensajeExcepcion = $excepcion->getMessage();

            echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
            echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
        } finally {
            unset($miDB);//Cerramos la conexión 
        }
    //declaracion de variables universales
    define("OBLIGATORIO", 1);
    define("OPCIONAL", 0);
    $entradaOK = true;


    //Array de errores inicializado a null
    $aErrores = ['DescDepartamento' => null,
                 'VolumenNegocio' => null];

    //Array del formulario inicializado a null
    $aFormulario = ['DescDepartamento' => null,
                    'VolumenNegocio' => null];

    if(isset($_REQUEST['aceptar'])){ //Si se ha enviado el formulario
        $aErrores['DescDepartamento'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescDepartamento'], 255, 1, OBLIGATORIO);
        $aErrores['VolumenNegocio'] = validacionFormularios::comprobarFloat($_REQUEST['VolumenNegocio'], PHP_FLOAT_MAX, PHP_FLOAT_MIN, OBLIGATORIO);

        //Se recorre el array de errores
        foreach ($aErrores as $campo => $error){
            if ($error != null) { // si los campos no estan vacios
                $entradaOK = false; // si hay algun error se le pone false y se rellenan los campos vacios 
                $_REQUEST[$campo] = "";
            }
        }
    }else{
        $entradaOK = false; //Si no se ha enviado el formulario
    }
    if($entradaOK){ //Si el formulario se ha enviado correctamente
        try {
            /* Establecemos la connection con pdo */
                            $miDB = new PDO(HOST, USER, PASSWORD);
                            /* configurar las excepcion */
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
;
            $sql = <<<EOD
               UPDATE Departamento SET 
               DescDepartamento=:DescDepartamento,
               VolumenNegocio=:VolumenNegocio 
               WHERE CodDepartamento=:CodDepartamento;
EOD;
            $consulta = $miDB->prepare($sql);//Preparar la consulta
            $parametros = [ ":DescDepartamento" => $_REQUEST['DescDepartamento'],
                            ":VolumenNegocio" => $_REQUEST['VolumenNegocio'],
                            ":CodDepartamento" => $_REQUEST['CodDepartamento']];

            $consulta->execute($parametros);//Introducimos los parametros en la consulta
            
            header('Location: '.'mtoDepartamentos.php');//Vuelve a la pagina anterior
            
        }catch (PDOException $excepcion){
            $errorExcepcion = $excepcion->getCode();
            $mensajeExcepcion = $excepcion->getMessage();

            echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
            echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
        } finally {
            unset($miDB);//Cerramos la conexión 
        }
    }else{ // Si el usuario no ha rellenado el formulario correctamente volvera a rellenarlo
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
        <div class="titulo">Mantenimiento de Departamentos - Editar Departamento</div>
    </header>
    <main class="mainEditar">
        <div class="contenido">
                <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'].'?CodDepartamentoEnCurso='.$_REQUEST['CodDepartamentoEnCurso'];?>" method="post" class="formularioEditar">
                    <div>
                        <label style="font-weight: bold;" class="CodigoDepartamento" for="CodDepartamento">Código de departamento: </label>
                        <input type="text" id="nombre" style="border: 0" name="CodDepartamento" value="<?php echo $_REQUEST['CodDepartamentoEnCurso'] ?>"readonly>
                        <br><br>

                        <label style="font-weight: bold;" class="DescripcionDepartamento" for="DescDepartamento">Descripción de departamento: </label>
                        <input type="text" id="DescDepartamento" style="background-color: white" name="DescDepartamento" value="<?php echo(isset($_REQUEST['aceptar']) ? ($aErrores['DescDepartamento']!=null ? $descripcionDepartamento: $_REQUEST['DescDepartamento']) : $descripcionDepartamento);?>">
                        <?php echo($aErrores['DescDepartamento']!=null ? "<span style='color:red'>".$aErrores['DescDepartamento']."</span>" : null); ?>
                        <br><br>
                        
                        <label style="font-weight: bold;" class="Fecha" for="Fecha">Fecha: </label>
                        <input type="text" id="Fecha" style="border: 0" name="Fecha" value="<?php echo $fechaBaja ?>"readonly>
                        <br><br>

                        <label style="font-weight: bold;" class="Volumen" for="VolumenNegocio">Volumen de negocio: </label>
                        <input type="text" id="VolumenNegocio" style="background-color: white" name="VolumenNegocio" value="<?php echo(isset($_REQUEST['aceptar']) ? ($aErrores['VolumenNegocio']!=null ? $volumenNegocio : $_REQUEST['VolumenNegocio']) : $volumenNegocio);?>">
                        <?php echo($aErrores['VolumenNegocio']!=null ? "<span style='color:red'>".$aErrores['VolumenNegocio']."</span>" : null); ?>
                        <br><br>
                    </div>
                    <div>
                        <input type="submit" style="background-color: rgba(17, 188, 20, 0.8)" value="Aceptar" name="aceptar" class="aceptar">
                    <input type="submit" style="background-color: rgba(207, 16, 16, 0.8)" value="Cancelar" name="cancelar" class="cancelar">
                    </div>
                </form>
            
        </div>
    </main>
</body>
</html>
<?php
    }
?>