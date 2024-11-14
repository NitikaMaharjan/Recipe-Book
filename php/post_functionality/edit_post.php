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
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo1.png" type="image/png">
        <style>
            /* Container styling */
            .edit_post_container {
                width: 60%;
                margin: 0 auto;
                padding: 20px;
                background-color: #f9f9f9;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                font-family: Arial, sans-serif;
            }

            /* General form styling */
            .edit_post_container form {
                display: flex;
                flex-direction: column;
            }

            /* Label styling */
            .edit_post_container label {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 5px;
                color: #333;
            }

            /* Input field styling */
            .edit_post_container input[type="text"],
            .edit_post_container input[type="file"],
            .edit_post_container textarea {
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 14px;
                width: 100%;
            }

            .edit_post_container input[type="file"] {
                padding: 5px 0;
            }

            /* Styling for buttons */
            .edit_post_container button {
                background-color: #f44336;
                color: #fff;
                border: none;
                padding: 5px 10px;
                margin-top: 5px;
                cursor: pointer;
                font-size: 14px;
                border-radius: 4px;
            }

            .edit_post_container button:hover {
                background-color: #d32f2f;
            }

            /* Add ingredient/step button styling */
            .edit_post_container img {
                cursor: pointer;
                margin-top: 10px;
            }

            /* Add ingredient/step field container styling */
            #ingredients_container,
            #steps_container {
                display: flex;
                flex-direction: column;
            }

            .ingredient-field,
            .step-field {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
            }

            .ingredient-field input,
            .step-field textarea {
                margin-right: 10px;
            }

            /* Input fields inside ingredient and step fields */
            .ingredient-field input,
            .step-field textarea {
                width: 90%;
                margin-right: 10px;
            }

            /* Ensure textarea is flexible */
            .step-field textarea {
                resize: vertical;
            }

            /* Form submit button styling */
            .edit_post_container input[type="submit"] {
                background-color: black;
                color: white;
                border: none;
                padding: 10px 20px;
                font-size: 16px;
                cursor: pointer;
                border-radius: 4px;
                margin-top: 20px;
            }

            .edit_post_container input[type="submit"]:hover {
                background-color: #ffbf17;
            }
            
            .back-button {
                position: fixed;
                background-color: #FFBF17;
                color: white;
                font-weight: bold;
                border: none;
                border-radius: 10px;
                padding: 10px 20px;
                cursor: pointer;
                transition: background-color 0.3s;
                top: 20px;
                left: 20px;
            }

        </style>
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
        <button onclick="go_back()" class="back-button">Go Back</button><br>    
        <h1 style="text-align:center">Edit your Recipe</h1>
        <div class="edit_post_container">
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
                <img onclick="addIngredientField()" width="30px" style="background-color:transparent;cursor: pointer;"
                src="/RecipeBook/Recipe-Book/buttons/add_button.png" title="Add Ingredient">
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
                <img onclick="addStepField()" width="30px" style="background-color:transparent;cursor: pointer;"
                src="/RecipeBook/Recipe-Book/buttons/add_button.png" title="Add Step">
                
                <br/><br/>

                <label for="post_keywords">Keywords:</label>
                <input type="text" name="post_keywords" id="post_keywords" value="<?php echo htmlspecialchars($row['post_keywords']); ?>" required/><br/><br/>

                <label for="post_category">Category:</label>
                <input type="text" name="post_category" id="post_category" value="<?php echo htmlspecialchars($row['post_category']); ?>" required/><br/><br/>

                <label for="post_text">Description :</label>
                <input type="text" name="post_text" id="post_text" value="<?php echo htmlspecialchars($row['post_text']); ?>" required/><br/><br/>

                <input type="submit" value="Update"/>
            </form>
        </div>
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