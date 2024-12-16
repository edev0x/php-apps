<?php
  /**
   * @author Edenilson Pineda
   * @link https://www.github.com/edev0x
   */
  require __DIR__ . '/utils/utils.php';

  define("UPLOAD_DIR", "fotos/");
  define("MAX_FILE_SIZE", isset($_REQUEST['MAX_FILE_SIZE']) ? (int) $_REQUEST['MAX_FILE_SIZE'] : 1002400);

  try {
    $data = Utils::readJson('properties/departamentos_sv.json');

    $options = $data["departamentos"] ?? [];
  } catch (Exception $e) {
    $options = [];
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inserci&oacute;n de vivienda</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

  <style>
    .label.is-required::after {
      content: ' *';
      color: red;
    }
  </style>
</head>

<body>
  <?php
      // Obtener valores introducidos por el formulario
      $insertar = $_REQUEST["insertar"];

      $error = false;
      $errores = [];
      $nombreFichero = "";

      // Validar datos
      if (isset($insertar)) {
        $address = $_REQUEST["address"];
        $housePrice = $_REQUEST["sellingPrice"];
        $houseSize = $_REQUEST["houseSize"];
        $houseType = $_REQUEST["houseType"];
        $department = $_REQUEST["department"];
        $totalBedrooms = $_REQUEST["totalBedrooms"];
        $extras = $_REQUEST["extras"];
        $comments = ($_REQUEST["comments"] != null && !empty($_REQUEST["comments"])) ? $_REQUEST["comments"] : "No hay observaciones";


        if (trim($address) == "") {
          $error = true;
          $errores["address"] = "¡Se requiere la dirección de la vivienda!";
        }

        if (!is_numeric($housePrice) || $housePrice < 0) {
          $errores["sellingPrice"] = "¡El precio debe ser un valor númerico!";
          $error = true;
        }

        if (!is_numeric($houseSize) || $houseSize < 0) {
          $errores["houseSize"] = "¡El tamaño debe ser un valor númerico!";
          $error = true;
        }
        
      }

      // Si los datos son correctos, procesar formulario
      if (isset($insertar) && $error == false) {
        // Mover foto a su ubicacion definitiva
        if (isset($_FILES["photoFile"]) && $_FILES["photoFile"]["error"] == UPLOAD_ERR_OK) {
          $fileName = time() . basename($_FILES["photoFile"]["name"]);
          $uploadFile = UPLOAD_DIR . $fileName;

          move_uploaded_file($_FILES['photoFile']['tmp_name'], $uploadFile);
        } else if ($_FILES["photoFile"]["error"] == UPLOAD_ERR_FORM_SIZE) {
          $maxSize = $_REQUEST["MAX_FILE_SIZE"];
          $errores["photoFile"] = "El tamaño del fichero supera el limite permitido ($maxSize bytes)";
          $error = true;
        } else if ($_FILES["photoFile"]["error"] = UPLOAD_ERR_NO_FILE) {
          $errores["photoFile"] = "No se ha seleccionado ningún archivo.";
          $error = true;
        } else {
          $errores["photoFile"] = "No se ha podido subir el fichero";
          $error = true;
        }

        $extrasFormatted =  isset($extras) ? implode(", ", $extras) : "No seleccion&oacute; extras.";
        
        echo <<<EOD
          <section class="section">
            <div class="container mx-2">
              <h1 class="title mb-2">Inserci&oacute;n de vivienda</h1>
              <p class="subtitle">
                Estos son los datos introducidos:
              </p>

              <div class="content is-normal">
                <ul>
                  <li><strong>Tipo:</strong> $houseType</li>
                  <li><strong>Departamento:</strong> $department</li>
                  <li><strong>Direcci&oacute;n:</strong> $address</li>
                  <li><strong>N&uacute;mero de dormitorios:</strong> $totalBedrooms</li>
                  <li><strong>Precio:</strong> $$housePrice</li>
                  <li><strong>Tama&ntilde;o:</strong> $houseSize m<sup>2</sup></li>
                  <li><strong>Extras:</strong> $extrasFormatted </li>
                  <li><strong>Foto:</strong> <a href="fotos/$fileName">$fileName</a></li>
                  <li><strong>Observaciones:</strong> $comments </li>
                </ul>
              </div>

              <div class="field is-horizontal mt-4">
                  <a href="/" class="button is-normal is-link" id="insertarNuevo">Insertar otra vivienda</a>
              </div>
            </div>
          </section>
        EOD;

      } 
      else 
      {
  ?>
    <section class="section">
      <div class="container mx-2">

        <h1 class="title mb-2">Inserci&oacute;n de vivienda</h1>
        <p class="subtitle">
          Introduzca los datos de la vivienda:
        </p>

        <div class="p-2">
          <form action="index.php" method="post" id="state-form" enctype="multipart/form-data">

            <!-- Input tipo de vivienda -->
            <div class="field is-horizontal mb-4">
              <div class="field-label has-text-left-tablet">
                <label class="label" for="houseType">Tipo de vivienda:</label>
              </div>
              <div class="field-body">
                <div class="field">
                  <div class="control">
                    <div class="select is-narrow-desktop">
                      <select id="houseType" name="houseType" style="min-width: 250px">
                        <option value="Casa">Casa</option>
                        <option value="Apartamento">Apartamento</option>
                        <option value="Rancho de Playa">Rancho de Playa</option>
                        <option value="Terreno">Terreno</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input departamento -->
            <div class="field is-horizontal mb-4">
              <div class="field-label has-text-left-tablet">
                <label class="label" for="department">Departamento:</label>
              </div>
              <div class="field-body">
                <div class="field">
                  <div class="control">
                    <div class="select is-narrow-desktop">
                      <select id="department" name="department" style="min-width: 250px">
                        <?php foreach ($options as $opt): ?>
                          <option value="<?= htmlspecialchars($opt["nombre"]) ?>">
                            <?= htmlspecialchars($opt["nombre"]) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input direccion -->
            <div class="field is-horizontal mb-4">
              <div class="field-label has-text-left-tablet">
                <label class="label is-required" for="address">Dirección:</label>
              </div>
              <div class="field-body">
                <div class="field">
                  <div class="control">
                    <input type="text" class="input" id="address" name="address" style="width: 250px"
                      placeholder="Ingrese su direccion" />
                  </div>
                  <?php if ($errores["address"] != "")
                    print ("<p class='help is-danger'>" . $errores["address"] . "</p>") ?>
                  </div>
                </div>
            </div>

            <!-- Input totalBedrooms -->
            <div class="field is-horizontal mb-4">
                <div class="field-label has-text-left-tablet">
                  <label class="label" for="totalBedrooms">N&uacute;mero de dormitorios:</label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <div class="control">
                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                      <label class="radio px-2">
                        <input type="radio" name="totalBedrooms" id="totalBedrooms" value="<?php echo $i ?>"/>
                        <?php echo $i ?>
                      </label>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input sellingPrice -->
            <div class="field is-horizontal mb-4">
              <div class="field-label has-text-left-tablet">
                <label class="label is-required" for="sellingPrice">Precio:</label>
              </div>
              <div class="field-body">
                <div class="field">
                  <div class="control has-icons-left">
                    <input type="number" id="sellingPrice" name="sellingPrice" min="0" class="input" step="0.1"
                      style="width: 250px" />
                    <span class="icon is-small is-left">
                      <i class="fas fa-dollar-sign"></i>
                    </span>
                  </div>
                  <?php if ($errores["sellingPrice"] != "")
                    print ("<p class='help is-danger'>" . $errores["sellingPrice"] . "</p>") ?>
                  </div>
                </div>
            </div>

            <!-- Input size -->
            <div class="field is-horizontal mb-4">
                <div class="field-label has-text-left-tablet">
                  <label class="label is-required" for="houseSize">Tama&ntilde;o:</label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <div class="control has-icons-left">
                      <input type="number" id="houseSize" name="houseSize" min="0" class="input" style="width: 250px" />
                      <span class="icon is-small is-left">
                        m²
                      </span>
                    </div>
                  <?php if ($errores["houseSize"] != "")
                    print ("<p class='help is-danger'>" . $errores["houseSize"] . "</p>") ?>
                  </div>
                </div>
            </div>


            <!-- Input extras -->
            <div class="field is-horizontal mb-4">
                <div class="field-label has-text-left-tablet">
                  <label class="label" for="extras">Extras (marque los que procedan):</label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <div class="control has-icons-left">
                      <div class="checkboxes">
                        <label class="checkbox">
                          <input type="checkbox" name="extras[]" value="piscina"/>
                          Piscina
                        </label>
                        <label class="checkbox">
                          <input type="checkbox" name="extras[]" value="jardin"/>
                          Jard&iacute;n
                        </label>
                        <label class="checkbox">
                          <input type="checkbox" name="extras[]" value="garage" />
                          Garage
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <!-- Input foto -->
            <div class="field is-horizontal mb-4">
                <div class="field-label has-text-left-tablet">
                  <label class="label" for="photoFile">Foto:</label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <div class="control has-icons-left">
                      <div class="file has-name is-small">
                        <label class="file-label">
                          <input type="hidden" name="MAX_FILE_SIZE" value="1002400" />
                          <input class="file-input" id="photoFile" name="photoFile" type="file" />
                          <span class="file-cta">
                            <span class="file-icon">
                              <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">Seleccione un archivo...</span>
                          </span>
                          <span class="file-name" id="file-name">No ha seleccionado ningún
                            archivo</span>
                        </label>
                      </div>
                    </div>
                  <?php if ($errores["photoFile"] != "")
                    print ("<p class='help is-danger'>" . $errores["photoFile"] . "</p>") ?>
                  </div>
                </div>
            </div>

            <!-- Input observaciones -->
            <div class="field is-horizontal mb-6">
                <div class="field-label has-text-left-tablet">
                  <label class="label" for="comments">Observaciones:</label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <div class="control">
                      <textarea class="textarea" placeholder="" style="width: 400px; min-width: 400px" rows="5"
                        id="comments" name="comments"></textarea>
                    </div>
                  </div>
                </div>
            </div>

            <div class="field is-horizontal">
                <button class="button is-normal is-success" name="insertar" type="submit">Insertar
                  vivienda</button>
            </div>

            </form>
          </div>
        </div>
      </section>

      <script>
        const fileInput = document.getElementById("photoFile");
        const fileName = document.getElementById("file-name");

        fileInput.addEventListener("change", () => {
          if (fileInput.files.length > 0) {
            fileName.textContent = fileInput.files[0].name;
          } else {
            fileName.textContent = "No ha seleccionado ningún archivo"
          }
        });
      </script>
    <?php
  }
  ?>
</body>
</html>