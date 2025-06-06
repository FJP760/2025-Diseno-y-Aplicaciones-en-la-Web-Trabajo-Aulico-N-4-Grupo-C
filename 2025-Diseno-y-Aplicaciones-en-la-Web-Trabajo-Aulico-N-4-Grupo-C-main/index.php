<?php
session_start();

// Aseguramos que siempre exista, para no romper la inserción en el <script>
$mostrarRespuesta = false;

// Si vengo por GET y existe un bloque de éxito en sesión, lo asigno a $respuestaHTML
// Esto permite que nuestro mensaje se vea solo una vez
$respuestaHTML = '';
if (isset($_SESSION['respuestaHTML'])) {
    $respuestaHTML = $_SESSION['respuestaHTML'];
    unset($_SESSION['respuestaHTML']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['c'])) {
    $errores = [];
    $nombre  = isset($_POST['nombre'])     ? trim(htmlspecialchars($_POST['nombre']))     : '';
    $email   = isset($_POST['correo'])     ? trim(htmlspecialchars($_POST['correo']))     : '';
    $asunto  = isset($_POST['comboasunto'])? trim(htmlspecialchars($_POST['comboasunto'] )): '';
    $mensaje = isset($_POST['areatext'])   ? trim(htmlspecialchars($_POST['areatext']))   : '';

    //mb_Strlen = longitud del string (incluyendo tildes o letras como ñ)
    if (empty($nombre) || mb_strlen($nombre) < 3) {
        $errores[] = "El campo 'Nombre' es obligatorio y debe tener al menos 3 caracteres.";
    }
    // filter_var = funcion que filtra la variable en cuanto diferentes escenarios, en este caso, un email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El campo 'Email' es obligatorio y debe tener un formato válido.";
    }
    //!in_array= verifica que el asunto que elegimos coincida con los permitidos
    $valores_permitidos = ['consulta', 'sugerencia', 'reclamo'];
    if (empty($asunto) || !in_array($asunto, $valores_permitidos, true)) {
        $errores[] = "Debes seleccionar un asunto válido.";
    }
    if (empty($mensaje) || mb_strlen($mensaje) > 500) {
        $errores[] = "El mensaje es obligatorio y no puede superar 500 caracteres.";
    }

    // Si NO hay errores, construyo el HTML y lo paso a la sesión,.
    if (empty($errores)) {
        $html  = '<div class="exito-formulario">';
        $html .= '  <h3>¡Gracias por contactarnos, ' . $nombre . '!</h3>';
        $html .= '  <p>Hemos recibido tu <strong>' . $asunto . '</strong> y te responderemos a <strong>' . $email . '</strong> en breve.</p>';
        $html .= '  <p>Tu mensaje:</p>';
        $html .= '  <blockquote>' . nl2br($mensaje) . '</blockquote>';
        $html .= '</div>';

        // Lo almaceno en sesión y redirijo al mismo archivo (para convertir POST → GET)
        $_SESSION['respuestaHTML'] = $html;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Don Tobi</title>
    <meta charset="UTF-8">
    <meta name="description" content="Tienda de mascotas Don Tobi">
    <meta name="author"
        content="Erreguerena Agustin Iñaki, Piloni Fabrizio Julian, Fernandez Lautaro Agustin, Fraga Facundo Roman">
    <link href="estilos.css" rel="stylesheet">
    <script>

        window.onload = function () {
            const btnEnviar = document.getElementById("btnEnviar");
            const btnCalcular = document.getElementById("btnCalcular");
            const textareaMensaje = document.getElementById("mensaje");
            const caja = document.getElementsByClassName("caja")[0];

            // Solo agregar eventos si el formulario no ha sido enviado con éxito
            <?php if (!$mostrarRespuesta): ?>
                btnEnviar.addEventListener("click", () => {
                    const email = document.getElementById("email");
                    const nombre = document.getElementById("nombre");
                    const asunto = document.getElementById("asunto");
                    generarMensaje(nombre, asunto);
                });
            <?php endif; ?>

            btnEnviar.addEventListener("click", () => {
                const email = document.getElementById("email");
                const nombre = document.getElementById("nombre");
                const asunto = document.getElementById("asunto");
                validarEmail(email);
                generarMensaje(nombre, asunto);
            });
            btnCalcular.addEventListener("click", () => {
                const precio = document.getElementById("precio");
                const descuento = document.getElementById("descuento");
                const precioFinal = calcularDescuento(precio, descuento);
                if (precioFinal != -1) {
                    alert("El precio final es de $" + precioFinal);
                }
            });
            textareaMensaje.addEventListener("keyup", () => {
                actualizarContador();
            });
            caja.addEventListener("mouseover", () => {
                caja.style.backgroundColor = "#0000FF";
            });

            caja.addEventListener("mouseout", () => {
                caja.style.backgroundColor = "#333";
            });

        };

        function validarEmail(email) {
            let valEmail = email.value;
            const expReg = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (expReg.test(valEmail)) {
                alert("El email es valido: " + valEmail);
            } else {
                alert("El email no es valido" + valEmail);
            }
        }
        function calcularDescuento(precio, porcentaje) {
            let valPrecio = parseFloat(precio.value);
            let valPorcentaje = parseFloat(porcentaje.value);
            if (valPrecio < 0 || isNaN(valPrecio)) {
                alert("El precio debe ser mayor a 0");
                return -1;
            }
            if (valPorcentaje < 0 || valPorcentaje > 100 || isNaN(valPorcentaje)) {
                alert("El valor del descuento debe estar entre 0 y 100");
                return -1;
            }
            return valPrecio - (valPrecio * (valPorcentaje / 100));
        }

        function generarMensaje(nombre, asunto) {
            let valNombre = nombre.value;
            let valAsunto = asunto.value;
            console.log("Gracias " + valNombre + " por contactarnos sobre su " + valAsunto + ". Te responderemos pronto")
        }

        function actualizarContador() {
            const textarea = document.getElementById("mensaje");
            const parrafoContador = document.getElementById("contador");
            let cantCarac = textarea.value.length
            parrafoContador.textContent = "Cantidad de caracteres: " + cantCarac;
        }
    </script>
</head>


<header>
    <div class="logo-contenedor">
        <img src="https://i.pinimg.com/736x/9d/91/6a/9d916a7e65a33b6ed2c88d6cdb32ca38.jpg" alt="logo Don Tobi"
            width="100" height="100">
        <h1>Tienda de mascotas Don Tobi</h1>
    </div>
</header>



<main>

    <div class="formulario">
        <label>Formulario</label>
        <form method="POST">
            <input type="text" id="nombre" placeholder="Ingrese su nombre" name="nombre">
            <input type="email" id="email" placeholder="Ingrese su email" name="correo">
            <label>Asunto</label>
            <select id="asunto" name="comboasunto">
                <option value="consulta">Consulta</option>
                <option value="sugerencia">Sugerencia</option>
                <option value="reclamo">Reclamo</option>
            </select>
            <textarea id="mensaje" placeholder="Ingrese su mensaje" name="areatext"></textarea>
            <p id="contador">Cantidad de caracteres: 0</p>
            <input type="submit" value="Enviar" id="btnEnviar" name="c">
        </form>
        
         <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['c'])): ?>
            <?php if (!empty($errores)): ?>
                <div class="errores-formulario">
                    <h3>Se encontraron los siguientes errores:</h3>
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif ($mostrarRespuesta): ?>
                <?php echo $respuestaHTML; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        echo $respuestaHTML;
        ?>
        
    </div>
    <div class="formulario">
        <label>Calcular Descuento</label>
        <form action="">
            <input type="text" id="precio" placeholder="Ingrese el precio">
            <input type="number" id="descuento" min="0" max="100" placeholder="Ingrese el descuento">
            <input type="button" value="Calcular" id="btnCalcular">
        </form>
    </div>
    <div class="caja">

    </div>
    <div class="navegacion">
        <h2>Secciones</h2>
        <nav>
            <ul>
                <li><a href="#Perritos" target="_self">Perritos</a></li>
                <li><a href="#Gatitos" target="_self">Gatitos</a></li>
                <li><a href="#Otros" target="_self">Otros</a></li>
            </ul>
        </nav>
    </div>


    <div class="tabla_precios">
        <table>
            <tr>
                <th>CATEGORÍA</th>
                <th>PRODUCTO</th>
                <th>PRECIO</th>
            </tr>
            <tr>
                <td>Perros</td>
                <td>Alimento balanceado adulto (15kg)</td>
                <td>$10.500</td>
            </tr>
            <tr>
                <td>Perros</td>
                <td>Juguete mordedor de cuerda</td>
                <td>$1.200</td>
            </tr>
            <tr>
                <td>Perros</td>
                <td>Correa retráctil</td>
                <td>$3.500</td>
            </tr>
            <tr>
                <td>Gatos</td>
                <td>Alimento seco premium (7kg)</td>
                <td>$7.800</td>
            </tr>
            <tr>
                <td>Gatos</td>
                <td>Arenas sanitarias perfumadas</td>
                <td>$2.100</td>
            </tr>
            <tr>
                <td>Gatos</td>
                <td>Rascador con pelotas</td>
                <td>$4.250</td>
            </tr>
            <tr>
                <td>Otros</td>
                <td>Alimento para peces tropicales (frasco 250g)</td>
                <td>$950</td>
            </tr>
            <tr>
                <td>Otros</td>
                <td>Jaula para hámster con ruedas</td>
                <td>$5.000</td>
            </tr>
            <tr>
                <td>Otros</td>
                <td>Bañera para aves medianas</td>
                <td>$1.300</td>
            </tr>
        </table>
    </div>



    <section id="Perritos">
        <h2>Perritos</h2>
        <div class="producto_contenedor">
            <article class="producto">
                <h3>Alimento balanceado adulto (15kg)</h3>
                <p>Precio: $10.500</p>
                <img src="imagenes/alimento balanceado perro.webp" alt="Alimento para perro" width="200" height="200">
            </article>
            <article class="producto">
                <h3>Juguete mordedor de cuerda</h3>
                <p>Precio: $1.200</p>
                <img src="imagenes/juguete cuerda.jpg" alt="Juguete para perro" width="200" height="200">
            </article>
            <article class="producto">
                <h3>Correa retráctil</h3>
                <p>Precio: $3.500</p>
                <img src="imagenes/correa retractil.webp" alt="Correa para perro" width="200" height="200">
            </article>
        </div>
    </section>


    <section id="Gatitos">
        <h2>Gatitos</h2>
        <div class="producto_contenedor">
            <article class="producto">
                <h3>Alimento seco premium (7kg)</h3>
                <p>Precio: $7.800</p>
                <img src="imagenes/alimento seco gato.png.avif" alt="Alimento para gato" width="200" height="200">
            </article>
            <article class="producto">
                <h3>Arenas sanitarias perfumadas</h3>
                <p>Precio: $2.100</p>
                <img src="imagenes/arena sanitaria gato.jpg" alt="Arena sanitaria para gato" width="200" height="200">
            </article>
            <article class="producto">
                <h3>Rascador con pelotas</h3>
                <p>Precio: $4.250</p>
                <img src="imagenes/rascador gatos.png" alt="Rascador para gato" width="200" height="200">
            </article>
        </div>
    </section>



    <section id="Otros">
        <h2>Otros</h2>
        <div class="producto_contenedor">
            <article class="producto">
                <h3>Alimento para peces tropicales (250g)</h3>
                <p>Precio: $950</p>
                <img src="imagenes/alimento para peces.jpg" alt="Alimento para peces" width="200" height="200">
            </article>
            <article class="producto">
                <h3>Jaula para hámster con ruedas</h3>
                <p>Precio: $5.000</p>
                <img src="imagenes/jaula para hamster.webp" alt="Jaula para hámster" width="200" height="200">
            </article>
            <article class="producto">
                <h3>Bañera para aves medianas</h3>
                <p>Precio: $1.300</p>
                <img src="imagenes/bañera para loro.webp" alt="Bañera para aves" width="200" height="200">
            </article>
        </div>
    </section>
</main>



<footer>
    <p><strong>Don Tobi</strong></p>
    <p><strong>Dirección:</strong> Avenida 213, Ciudad Verde</p>
    <p><strong>Tel:</strong> +54 376 400-0000</p>
    <p><strong>Email:</strong> <a href="mailto:donTobi@gmail.com">donTobi@gmail.com</a></p>
    <p>&copy; 2025 Don Tobi. Todos los derechos reservados.</p>
</footer>
</body>

</html>