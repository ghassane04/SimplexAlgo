<?php
class Simplex {
    // Variables privées pour stocker le tableau du simplex, le nombre de lignes et de colonnes, et les indices des variables de base.
    private $tableau; 
    private $rows; 
    private $cols; 
    private $basicVars;
    private $numDecisionVars;  // Declaration of the variable

    public function __construct($objective, $constraints) {
        $this->numDecisionVars = count($objective); // Initialization of numDecisionVars
        $this->initializeTableau($objective, $constraints);
    }

    private function initializeTableau($objective, $constraints) {
        $numConstraints = count($constraints);
        $numVariables = count($objective);
        $this->cols = $numVariables + $numConstraints + 1;
        $this->rows = $numConstraints + 1;
        $this->tableau = array_fill(0, $this->rows, array_fill(0, $this->cols, 0));
        $this->basicVars = array_fill(0, $numConstraints, $numVariables);

        for ($i = 0; $i < $numVariables; $i++) {
            $this->tableau[$this->rows - 1][$i] = -$objective[$i];
        }
        
        for ($i = 0; $i < $numConstraints; $i++) {
            for ($j = 0; $j < $numVariables; $j++) {
                $this->tableau[$i][$j] = $constraints[$i][$j];
            }
            $this->tableau[$i][$numVariables + $i] = 1; 
            $this->tableau[$i][$this->cols - 1] = $constraints[$i][$numVariables];
            $this->basicVars[$i] = $numVariables + $i;
        }
    }

    public function solve() {
        echo "<style>table { width: 50%; margin: 20px auto; border-collapse: collapse; } th, td { border: 1px solid #000; padding: 8px; text-align: center; }</style>";
        $iteration = 0;
        $this->printTableau(); 
        $this->printStatus();

        while (true) {
            $pivotCol = $this->findPivotColumn();
            if ($pivotCol == -1) {
                echo "<p>Aucun coefficient négatif dans la ligne de l'objectif, solution optimale trouvée.</p>";
                $this->printStatus(); // Display the final status of the variables
                break;
            }

            $pivotRow = $this->findPivotRow($pivotCol);
            if ($pivotRow == -1) {
                echo "<p>Solution non limitée détectée.</p>";
                return;
            }

            $this->formNextTableau($pivotRow, $pivotCol);
            echo "<h2>Itération " . (++$iteration) . " - Pivot à la ligne : " . ($pivotRow + 1) . ", Colonne : " . ($pivotCol + 1) . "</h2>";
            $this->printTableau($pivotRow, $pivotCol);
            $this->printStatus();
            $this->basicVars[$pivotRow] = $pivotCol;
        }
        $this->displayResults();
    }

    private function printTableau($pivotRow = null, $pivotCol = null) {
        echo "<table>";
        for ($i = 0; $i < $this->rows; $i++) {
            echo "<tr>";
            for ($j = 0; $j < $this->cols; $j++) {
                $style = ($i == $pivotRow && $j == $pivotCol) ? ' style="background-color: #ff0000;"' : '';
                echo "<td" . $style . ">" . round($this->tableau[$i][$j], 2) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    private function printStatus() {
        echo "<p>Variables de base: ";
        foreach ($this->basicVars as $var) {
            if ($var < $this->numDecisionVars) {
                echo "x" . ($var + 1) . " ";
            } else {
                echo "e" . ($var - $this->numDecisionVars + 1) . " ";
            }
        }
        echo "</p><p>Variables hors base: ";
        for ($i = 0; $i < $this->cols - 1; $i++) {
            if (!in_array($i, $this->basicVars)) {
                if ($i < $this->numDecisionVars) {
                    echo "x" . ($i + 1) . " ";
                } else {
                    echo "e" . ($i - $this->numDecisionVars + 1) . " ";
                }
            }
        }
        echo "</p><p>Z = " . round($this->tableau[$this->rows - 1][$this->cols - 1], 2) . "</p>";
    }

    private function displayResults() {
        echo "<h2>Valeurs Optimales:</h2>";
        $variableValues = array_fill(0, $this->cols - $this->rows, 0);
        for ($i = 0; $i < $this->rows - 1; $i++) {
            if ($this->basicVars[$i] < count($variableValues)) {
                $variableValues[$this->basicVars[$i]] = $this->tableau[$i][$this->cols - 1];
            }
        }
        for ($i = 0; $i < count($variableValues); $i++) {
            echo "<p>x" . ($i + 1) . " = " . round($variableValues[$i], 2) . "</p>";
        }
        echo "<p>Z = " . round($this->tableau[$this->rows - 1][$this->cols - 1], 2) . "</p>";
    }


    private function findPivotColumn() {
        // Trouve le premier coefficient négatif dans la dernière ligne pour déterminer la colonne de pivot.
        $lastRow = $this->tableau[$this->rows - 1];
        $pivotCol = -1;
        $lowest = 0;
        for ($i = 0; $i < $this->cols - 1; $i++) {
            if ($lastRow[$i] < $lowest) {
                $lowest = $lastRow[$i];
                $pivotCol = $i;
            }
        }
        return $pivotCol;
    }

    private function findPivotRow($pivotCol) {
        // Utilise la règle du rapport minimum pour trouver la ligne de pivot.
        $minRatio = PHP_INT_MAX;
        $pivotRow = -1;
        for ($i = 0; $i < $this->rows - 1; $i++) {
            if ($this->tableau[$i][$pivotCol] > 0) {
                $ratio = $this->tableau[$i][$this->cols - 1] / $this->tableau[$i][$pivotCol];
                if ($ratio < $minRatio) {
                    $minRatio = $ratio;
                    $pivotRow = $i;
                }
            }
        }
        return $pivotRow;
    }

    private function formNextTableau($pivotRow, $pivotCol) {
        // Normalise la ligne de pivot pour que le pivot soit 1, et élimine les autres éléments dans la colonne de pivot.
        $pivot = $this->tableau[$pivotRow][$pivotCol];
        for ($j = 0; $j < $this->cols; $j++) {
            $this->tableau[$pivotRow][$j] /= $pivot;
        }
        for ($i = 0; $i < $this->rows; $i++) {
            if ($i != $pivotRow) {
                $multiplier = $this->tableau[$i][$pivotCol];
                for ($j = 0; $j < $this->cols; $j++) {
                    $this->tableau[$i][$j] -= $multiplier * $this->tableau[$pivotRow][$j];
                }
            }
        }
    }}
?>
