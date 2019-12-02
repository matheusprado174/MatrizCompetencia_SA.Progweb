<?php
// conecta ao banco
try {
  $conexao = new PDO("mysql:host=localhost;dbname=matriz", "root", "");
  $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conexao->exec("set names utf8");
} catch (PDOException $erro) {
  echo "Erro na conexão:".$erro->getMessage();
}

// verifica se mandou os dados via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $id = (isset($_POST["id"]) && $_POST["id"] != null) ? $_POST["id"] : "";
  $nome = (isset($_POST["nome"]) && $_POST["nome"] != null) ? $_POST["nome"] : "";
  $setor = (isset($_POST["setor"]) && $_POST["setor"] != null) ? $_POST["setor"]:"";
  $competencia = (isset($_POST["competencia"]) && $_POST["competencia"] != null) ? $_POST["competencia"] : "";
}elseif(!isset($id)) {
  $id = (isset($_GET["id"]) && $_GET["id"] != null) ? $_GET["id"] : "";
  $nome = NULL;
  $setor = NULL;
  $competencia = NULL;
}
// CREATE  UPDATE
if (isset($_REQUEST["act"]) && $_REQUEST["act"] == "save" && $nome != "") {
  try{
    if ($id != ""){
      $stmt = $conexao->prepare("UPDATE colaborador SET nome=?, setor=?, competencia=? WHERE id = ? ");
      $stmt->bindParam(4, $id);
    } else {
      $stmt = $conexao->prepare("INSERT INTO colaborador (nome, setor, competencia) VALUES (?, ?, ?)");
    }
    $stmt->bindParam(1, $nome);
    $stmt->bindParam(2, $setor);
    $stmt->bindParam(3, $competencia);

    if ($stmt->execute()){
      if ( $stmt->rowCount() > 0){
        echo "Dados cadastrados com sucesso!";
        $id = null;
        $nome = null;
        $setor = null;
        $competencia = null;
      } else {
        echo "Erro ao tentar efetivar cadastro";
      } 
    } else{
      throw new PDOException("Erro: não foi possivel executar a instrução sql");
    }
  } catch (PDOException $erro){
    echo "Erro:".$erro->getMessage();
  }
}


if (isset($_REQUEST["act"]) && $_REQUEST["act"] == "upd" && $id != ""){
  try{
    $stmt = $conexao->prepare("SELECT * FROM colaborador WHERE id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    if ($stmt->execute()){
      $rs = $stmt->fetch(PDO::FETCH_OBS);
      $id = $rs->id;
      $nome = $rs->nome;
      $setor = $rs->setor;
      $celular = $rs->competencia;
    } else {
      throw new PDOException("Erro: não foi possivel exectuar instrução sql");
    }
  } catch (PDOException $erro){
    echo "Erro: ".$erro->getMessage();
  }
}

if (isset($_REQUEST["act"]) && $_REQUEST["act"] == "del" && $id != "") {
  try {
    $stmt = $conexao->prepare("DELETE FROM colaborador WHERE id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    if ($stmt->execute()){
      echo "Registro excluido com exito";
      $id = null;
    } else {
      throw new PDOException("Erro: Nao foi possivel executar instrução sql");
    }
  } catch (PDOException $erro) {
    echo "Erro: ".$erro->getMessage();
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Matriz de competência</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css">
</head>

<body>
  <div class="container">
    <form action="?act=save" method="POST" name="form1" id="formula">
      <h1>matriz de competência</h1>

      <input type="hidden" name="id" <?php
                 
                // Preenche o id no campo id com um valor "value"
                if (isset($id) && $id != null || $id != "") {
                    echo "value=\"{$id}\"";
                }
                ?> />
      Nome:
      <input type="text" name="nome" <?php
 
               // Preenche o nome no campo nome com um valor "value"
               if (isset($nome) && $nome != null || $nome != "") {
                   echo "value=\"{$nome}\"";
               }
               ?> />
      Setor:
      <input type="text" name="setor" <?php
 
               // Preenche o email no campo email com um valor "value"
               if (isset($setor) && $setor != null || $setor != "") {
                   echo "value=\"{$setor}\"";
               }
               ?> />
      Competencia:
      <input type="text" name="competencia" <?php
 
               // Preenche o celular no campo celular com um valor "value"
               if (isset($competencia) && $competencia != null || $competencia != "") {
                   echo "value=\"{$competencia}\"";
               }
               ?> />
      <input type="submit" value="salvar" class="btn waves-effect waves-light" />


    </form>
  </div>
  <div class="container">
    <table class="striped">
      <tr>
        <th>Nome</th>
        <th>Setor</th>
        <th>Competencia</th>
        <th>Ações</th>
      </tr>
  </div>
  <?php
 
                // Bloco que realiza o papel do Read - recupera os dados e apresenta na tela
                try {
                    $stmt = $conexao->prepare("SELECT * FROM colaborador");
                    if ($stmt->execute()) {
                        while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo "<tr>";
                            echo "<td>".$rs->nome."</td><td>".$rs->setor."</td><td>".$rs->competencia
                                       ."</td><td><center><a href=\"?act=upd&id=".$rs->id."\">[Alterar]</a>"
                                       ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
                                       ."<a href=\"?act=del&id=".$rs->id."\">[Excluir]</a></center></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "Erro: Não foi possível recuperar os dados do banco de dados";
                    }
                } catch (PDOException $erro) {
                    echo "Erro: ".$erro->getMessage();
                }
                ?>
  </table>
</body>

</html>