<html>
<head>
    <title>
        Memcache test
    </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
    <header>
      <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <a class="nav-link" href="?action=">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?action=stats">Stats</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?action=keys">Keys</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?action=test">Test</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?action=flush">Flush</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?action=servers">Servers</a>
            </li>
          </ul>
        </div>
      </nav>
    </header>

    <main class="container">
        <div class="my-3 p-3 bg-white rounded shadow-sm">
            <h6 class="border-bottom border-gray pb-2 mb-0">Content</h6>

            <?php foreach ($content as $line): ?>
            <div class="media text-muted pt-3">
                <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                    <?= nl2br($line); ?>
                </p>
            </div>

            <?php endforeach ?>
        </div>
    </main>

</body>
</html>
