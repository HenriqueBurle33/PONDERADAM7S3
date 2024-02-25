<!DOCTYPE html>
<html>
<head>
    <title>Jogadores de Basquete</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
            color: #495057;
            line-height: 1.6;
        }

        h1 {
            font-size: 2.5rem;
            color: white;
            text-align: center;
            padding: 40px 0;
            margin: 0;
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 90%;
            margin: 25px auto;
            border-collapse: collapse;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease-in-out;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            transition: all 0.3s ease-in-out;
        }

        th {
            background-color: #007bff;
            color: white;
            letter-spacing: 1px;
        }

        tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        form {
            width: 90%;
            margin: 25px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }

        input[type="text"], select {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s ease-in-out;
        }

        input[type="text"]:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 5px;
            color: white;
            background-color: #28a745;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<h1>Jogadores de Basquete</h1>

<?php
include "../inc/dbinfo.inc";

$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) echo "Falha ao conectar-se ao MySQL: " . mysqli_connect_error();

$database = mysqli_select_db($connection, DB_DATABASE);

VerifyPlayersTable($connection, DB_DATABASE);

if(isset($_POST['delete'])) {
  $delete_id = $_POST['delete_id'];
  $query = "DELETE FROM PLAYERS WHERE ID = $delete_id";

  if(mysqli_query($connection, $query)) {
      echo "<p>Jogador deletado com sucesso.</p>";
  } else {
      echo "<p>Erro ao deletar jogador.</p>";
  }

  echo "<meta http-equiv='refresh' content='0'>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete'])) {
    $name = htmlentities($_POST['NAME']);
    $shirt_number = intval($_POST['SHIRT_NUMBER']);
    $position = htmlentities($_POST['POSITION']);
    $team = htmlentities($_POST['TEAM']);
    $points_per_game = floatval($_POST['POINTS_PER_GAME']);

    if (!empty($name) && $shirt_number >= 0 && !empty($position) && !empty($team) && $points_per_game >= 0) {
        AddPlayer($connection, $name, $shirt_number, $position, $team, $points_per_game);
    }
}
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
    <table>
        <tr>
            <td>NOME</td>
            <td>NÚMERO DA CAMISA</td>
            <td>POSIÇÃO</td>
            <td>TIME</td>
            <td>PONTOS POR JOGO</td>
        </tr>
        <tr>
            <td><input type="text" name="NAME" maxlength="45" size="30" /></td>
            <td><input type="text" name="SHIRT_NUMBER" maxlength="10" size="15" /></td>
            <td>
                <select name="POSITION">
                    <option value="Armador">Armador</option>
                    <option value="Ala-Armador">Ala-Armador</option>
                    <option value="Ala">Ala</option>
                    <option value="Ala-Pivo">Ala-Pivô</option>
                    <option value="Pivo">Pivô</option>
                </select>
            </td>
            <td><input type="text" name="TEAM" maxlength="45" size="30" /></td>
            <td><input type="text" name="POINTS_PER_GAME" maxlength="10" size="15" /></td>
            <td><input type="submit" value="Adicionar Jogador" /></td>
        </tr>
    </table>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>NOME</th>
        <th>NÚMERO DA CAMISA</th>
        <th>POSIÇÃO</th>
        <th>TIME</th>
        <th>PONTOS POR JOGO</th>
        <th>DELETAR</th>
    </tr>
    <?php
    $result = mysqli_query($connection, "SELECT * FROM PLAYERS");

    while($query_data = mysqli_fetch_row($result)) {
        echo "<tr>";
        echo "<td>", $query_data[0], "</td>",
             "<td>", $query_data[1], "</td>",
             "<td>", $query_data[2], "</td>",
             "<td>", $query_data[3], "</td>",
             "<td>", $query_data[4], "</td>",
             "<td>", $query_data[5], "</td>";
        echo "<td>
                <form action='' method='POST'>
                    <input type='hidden' name='delete_id' value='{$query_data[0]}' />
                    <input type='submit' name='delete' value='Deletar' />
                </form>
              </td>";
        echo "</tr>";
    }

    mysqli_free_result($result);
    ?>
</table>

</body>
</html>

<?php

function AddPlayer($connection, $name, $shirt_number, $position, $team, $points_per_game) {
   $n = mysqli_real_escape_string($connection, $name);
   $s = intval($shirt_number);
   $p = mysqli_real_escape_string($connection, $position);
   $t = mysqli_real_escape_string($connection, $team);
   $ppg = floatval($points_per_game);

   $query = "INSERT INTO PLAYERS (NAME, SHIRT_NUMBER, POSITION, TEAM, POINTS_PER_GAME) VALUES ('$n', '$s', '$p', '$t', '$ppg');";

   if(!mysqli_query($connection, $query)) echo("<p>Erro ao adicionar dados do jogador.</p>");
}

function VerifyPlayersTable($connection, $dbName) {
  if(!TableExists("PLAYERS", $connection, $dbName)) {
     $query = "CREATE TABLE PLAYERS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         SHIRT_NUMBER INT,
         POSITION VARCHAR(45),
         TEAM VARCHAR(45),
         POINTS_PER_GAME FLOAT
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Erro ao criar tabela.</p>");
  }
}

function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
