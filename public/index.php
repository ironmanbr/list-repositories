<?php

require_once __DIR__ . '/../vendor/autoload.php';

$github = new \App\Lib\Github('symfony');
$repositories = $github->getRepositories();


$template = <<< TPL
<dl>
  <dt>%s</dt>
  <dd>%s</dd>
  <dd>%s</dd>
</dl>
TPL;

$htmlRepos = '<p>Nenhum repositório localizado</p>';

if ($repositories) {
    $htmlRepos = '<h1>Symfony - repositórios</h1>';
    foreach ($repositories as $repository) {
        $htmlRepos .= sprintf(
            $template,
            $repository[$github::REP_FULLNAME],
            $repository[$github::REP_URL],
            $repository[$github::REP_DESCRIPTION]
        );
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Repositórios</title>
</head>
<body>
    <?php echo $htmlRepos; ?>
</body>
</html>