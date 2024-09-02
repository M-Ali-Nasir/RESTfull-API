<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Dashboard</title>
</head>

<body>
  <nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1">REST Products API </span>
    </div>
  </nav>
  <div class="container mt-5">
    <div>
      <h4>API_Keys</h4>
      <div class="card">
        <div class="card-header">
          Existing keys:
        </div>
        <ul class="list-group list-group-flush">
          <?php
          if (!empty($userKeys)) {
            $count = 0;
            foreach ($userKeys as $key) {
              $count++;
              $id = $key['id'];
              $api = $key['api_key'];
              $name = $key['key_name'];
              echo "
                    <li class='list-group-item'>$count:&nbsp&nbsp$name 
                    &nbsp<span class='bg-secondary text-light'>$api</span>
                    <a href='src/ApiKeyController.php?key_id=$id' class='float-end text-secondary'><i class='fa-solid fa-trash'></i></a>
                    </li>
              ";
            }
          } else {
            echo "<p class='px-3 py-2 m-0'>No key found</p>";
          }

          ?>
        </ul>
      </div>
    </div>


    <div class="mt-5">
      <h4>Create New Keys</h4>
      <div class="card">
        <div class="card-header">
          Generate key:
        </div>
        <form action="src/ApiKeyController.php" method="POST">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <label for="keyName">Key Name:</label>
              <input type="text" id="keyName" class="form-control w-50" name="key_name" required>
            </li>
            <li class="list-group-item"><button type="submit" class="btn btn-secondary">Generate</button></li>
          </ul>
        </form>
      </div>
    </div>


  </div>






  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>