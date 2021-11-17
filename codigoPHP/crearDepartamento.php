<?php
    
if(isset($_REQUEST['cancelar'])){//Si pulsa el botón de cancelar
    header('Location: '.'departamento.php');//Redirigimos al usuario a la página inicial
}
   
    //Incluir la libreria de funciones para la validacion
                require_once '../core/210322ValidacionFormularios.php';
                require_once '../config/confDBPDO.php'; //Archivo con configuracion de PDO

    
    define("OBLIGATORIO", 1);
    define("OPCIONAL", 0);
    $entradaOK = true;


    //Declaramos el array de errores y lo inicializamos a null
    $aErrores = ['CodDepartamento' => null,
                 'DescDepartamento' => null,
                 'VolumenNegocio' => null];

    //Declaramos el array del formulario y lo inicializamos a null
    $aFormulario = ['CodDepartamento' => null,
                    'DescDepartamento' => null,
                    'VolumenNegocio' => null];

    if(isset($_REQUEST['aceptar'])){ //Si se ha enviado el formulario
        $aErrores['CodDepartamento'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['CodDepartamento'], 3, 3, OBLIGATORIO);
        $aErrores['DescDepartamento'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescDepartamento'], 255, 1, OBLIGATORIO);
        $aErrores['VolumenNegocio'] = validacionFormularios::comprobarFloat($_REQUEST['VolumenNegocio'], PHP_FLOAT_MAX, -PHP_FLOAT_MAX, OBLIGATORIO);
        try{
            
            /* Establecemos la connection con pdo */
                            $miDB = new PDO(HOST, USER, PASSWORD);
                            /* configurar las excepcion */
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlCod = "SELECT CodDepartamento from Departamento where CodDepartamento='{$_REQUEST['CodDepartamento']}'";
            $consultaCod = $miDB->prepare($sqlCod);//Preparamos la consulta
            $consultaCod->execute();//Ejecutamos la consulta

            if($consultaCod->rowCount()>0){//Si el código de departamento ya existe
                $aErrores['CodDepartamento'] = "Ese código de departamento ya existe";
            }
        }catch(PDOException $excepcion){
            $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
            $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

            echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
            echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
        } finally {
           unset($miDB); //cerramos la conexion con la base de datos
        } 
        
        // Recorremos el array de errores
        foreach ($aErrores as $campo => $error){
            if ($error != null) { // Comprobamos que el campo no esté vacio
                $entradaOK = false; // Si hay algún error   
                $_REQUEST[$campo] = "";//Limpiamos el campo
            }
        }
    }else{
        $entradaOK = false; // Si el usuario no ha enviado el formulario asignamos a entradaOK el valor false para que rellene el formulario
    }
    if($entradaOK){ //Formulario rellendado correctamente
        $aFormulario['CodDepartamento'] = strtoupper($_REQUEST['CodDepartamento']);
        $aFormulario['DescDepartamento'] = $_REQUEST['DescDepartamento'];
        $aFormulario['VolumenNegocio'] = $_REQUEST['VolumenNegocio'];

        try {
           /* Establecemos la connection con pdo */
                            $miDB = new PDO(HOST, USER, PASSWORD);
                            /* configurar las excepcion */
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = <<<EOD
               INSERT INTO Departamento (CodDepartamento, DescDepartamento, VolumenNegocio) VALUES 
                    (:CodDepartamento, :DescDepartamento, :VolumenNegocio); 
EOD;
            $consulta = $miDB->prepare($sql);//Preparamos la consulta
            $parametros = [ ":CodDepartamento" => $aFormulario['CodDepartamento'],
                            ":DescDepartamento" => $aFormulario['DescDepartamento'],
                            ":VolumenNegocio" => $aFormulario['VolumenNegocio'] ];

            $consulta->execute($parametros);//Rellenar la consulta
            header('Location: '.'mtoDepartamentos.php');//Una vez realizada lleva al usuario a la pagina departamento

        }catch (PDOException $excepcion){
            $errorExcepcion = $excepcion->getCode();
            $mensajeExcepcion = $excepcion->getMessage();

            echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
            echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
        } finally {
            unset($miDB);//Cerramos la conexión 
        }
    }else{//Si el formulario esta rellenado incorrectamente
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
        <div class="logo">Mantenimiento de Departamentos - Añadir Departamento</div>
    </header>
    <main class="mainEditar">
        <div class="contenido">
            <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="formularioAlta">
                <div>
                    <label style="font-weight: bold;" class="CodigoDepartamento" for="CodDepartamento">Código de departamento: </label>
                    <input type="text" id="CodDepartamento" name="CodDepartamento" value="<?php echo(isset($_REQUEST['CodDepartamento']) ? $_REQUEST['CodDepartamento'] : null); ?>">
                    <?php echo($aErrores['CodDepartamento']!=null ? "<span style='color:red'>".$aErrores['CodDepartamento']."</span>" : null); ?>
                    <br><br>

                    <label style="font-weight: bold;" class="DescripcionDepartamento" for="DescDepartamento">Descripción de departamento: </label>
                    <input type="text" id="DescDepartamento" name="DescDepartamento" value="<?php echo(isset($_REQUEST['DescDepartamento']) ? $_REQUEST['DescDepartamento'] : null);?>">
                    <?php echo($aErrores['DescDepartamento']!=null ? "<span style='color:red'>".$aErrores['DescDepartamento']."</span>" : null); ?>
                    <br><br>

                    <label style="font-weight: bold;" class="Volumen" for="VolumenNegocio">Volumen de negocio: </label>
                    <input type="text" id="VolumenNegocio" name="VolumenNegocio" value="<?php echo(isset($_REQUEST['VolumenNegocio']) ? $_REQUEST['VolumenNegocio'] : null);?>">
                    <?php echo($aErrores['VolumenNegocio']!=null ? "<span style='color:red'>".$aErrores['VolumenNegocio']."</span>" : null); ?>
                    <br><br>
                </div>
                <span class="atencion">Se convertira el codigo de departamento a letras MAYUSCULAS</span>
                <div>
                    <input type="submit" style="background-color: #a3f27b;" value="Aceptar" name="aceptar" class="aceptar">
                    <input type="submit" style="background-color: #f27b7b;" value="Cancelar" name="cancelar" class="cancelar">
                </div>
            </form>
        </div>
    </main>
</body>
</html>
<?php
    }
?>