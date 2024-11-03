<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['post_id'])){
        $post_id = (int)$_GET['post_id'];

        $sql = "SELECT * FROM post WHERE post_id = $post_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "<script>
                    alert ('No post found for the provided ID!!');
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert ('No post ID provided for editing!!');
              </script>";
        exit();
    }
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/styles.css">
        <script>
            function addIngredientField() {
                let container = document.getElementById("ingredients_container");
                let input = document.createElement("input");
                input.type = "text";
                input.name = "post_ingredients[]";
                input.placeholder = "Enter ingredient";
                container.appendChild(input);
                container.appendChild(document.createElement("br"));
            }

            function addStepField() {
                let container = document.getElementById("steps_container");
                let input = document.createElement("textarea");
                input.name = "post_instructions[]";
                input.placeholder = "Enter preparation step";
                container.appendChild(input);
                container.appendChild(document.createElement("br"));
            }
            function removeField(button) { 
                let div = button.parentElement; div.remove(); 
            }
        </script>
    </head>
    <body>
        <button onclick="go_back()" class="back-button">Go Back</button>
        <h1>Edit your Recipe</h1>
        <form action="/RecipeBook/Recipe-Book/php/post_functionality/update_post.php?post_id=<?php echo $post_id;?>" method="POST" enctype="multipart/form-data">

            <label for="post_image">Post Image:</label>
            <?php 
                if (($row['post_image'])) {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
                } else {
                    echo "No image available";
                } 
            ?><br/><br/>
            <input type="file" name="post_image" accept="image" id="post_image" value="<?php $row['post_image']; ?>"/><br/><br/>

            <label for="post_title">Post Title:</label>
            <input type="text" name="post_title" id="post_title" value="<?php echo htmlspecialchars($row['post_title']); ?>" required/><br/><br/>
            
            <label for="post_ingredients">Ingredients:</label> 
            <div id="ingredients_container">
                <?php 
                    $ingredients = explode(", ", $row['post_ingredients']); 
                    foreach ($ingredients as $ingredient) { 
                        echo "<div class='ingredient-field'>";
                        echo "<input type='text' name='post_ingredients[]' value='" . htmlspecialchars($ingredient) . "' required/>";
                        echo "<button type='button' onclick='removeField(this)'>Remove</button>";
                        echo "</div>";
                    } 
                ?>
            </div> 
            <button type="button" onclick="addIngredientField()">Add Ingredient</button>
            <br/><br/>

            <label for="post_instructions">Instructions:</label> 
            <div id="steps_container">
                <?php 
                    $steps = explode(", ", $row['post_instructions']); 
                    foreach ($steps as $step) {
                        echo "<div class='step-field'>";
                        echo "<textarea name='post_instructions[]' required>" . htmlspecialchars($step) . "</textarea>";
                        echo "<button type='button' onclick='removeField(this)'>Remove</button>";
                        echo "</div>";
                    } 
                ?>
            </div> 
            <button type="button" onclick="addStepField()">Add Step</button>
            <br/><br/>

            <label for="post_keywords">Keywords:</label>
            <input type="text" name="post_keywords" id="post_keywords" value="<?php echo htmlspecialchars($row['post_keywords']); ?>" required/><br/><br/>

            <label for="post_category">Category:</label>
            <input type="text" name="post_category" id="post_category" value="<?php echo htmlspecialchars($row['post_category']); ?>" required/><br/><br/>

            <label for="post_text">Description :</label>
            <input type="text" name="post_text" id="post_text" value="<?php echo htmlspecialchars($row['post_text']); ?>" required/><br/><br/>

            <input type="submit" value="Update"/>
        </form>
    </body>
    <script>
        function go_back(){
            window.history.back();
        }
    </script>
</html>

<?php
    $conn->close();
?>