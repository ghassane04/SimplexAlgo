<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résolveur Simplex</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 350px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        label {
            font-weight: bold;
            color: #34495e;
        }
        input, textarea {
            width: calc(100% - 16px);
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box; /* Includes padding and border in the element's total width and height */
        }
        button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #3498db;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>Résolveur Simplex</h1>
    <form action="result.php" method="post">
        <div>
            <label for="objective">Fonction Objectif (séparée par des virgules):</label>
            <input type="text" id="objective" name="objective" placeholder="ex. : 3,2" required>
        </div>
        <div>
            <label for="constraints">Contraintes (séparées par des virgules, une nouvelle ligne pour chaque):</label>
            <textarea id="constraints" name="constraints" placeholder="ex. : 2,1,18" required></textarea>
        </div>
        <button type="submit">Résoudre</button>
    </form>
</body>
</html>
