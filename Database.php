<?php
/*
 * PDO Database Class
 * Connect to database
 * Create prepared statement
 * Bind values
 * Return rows and results
 */

class Database
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $dbcharset = DB_CHARSET;

    private $pdo;
    private $stmt;
    private $error;
    public function __construct()
    {
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=" . $this->dbcharset;
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true,
        );

        // Create PDO instance
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    //  view table create
    public function searchView()
    {
        $stmt = $this->pdo->prepare('CREATE OR REPLACE VIEW `search_view` AS
                            SELECT categories.description, categories.name AS category_name, types.name AS type_name
                            FROM categories JOIN incomes ON categories.id = incomes.category_id
                            JOIN types ON types.id = categories.type_id
                            UNION
                            SELECT categories.description, categories.name AS category_name, types.name AS type_name
                            FROM categories  JOIN expenses ON categories.id = expenses.category_id
                            JOIN types ON types.id = categories.type_id
');
        $success = $stmt->execute();
        // $row     = $stmt->fetch(PDO::FETCH_ASSOC);
        // return ($success) ? $row : [];
    }
    // view table create

    /*
     * @param integer $id
     * @return Model
     */
    public function getById($table, $id)
    {
        $stm = $this->pdo->prepare('SELECT * FROM ' . $table . ' WHERE `id` = :id');
        $stm->bindValue(':id', $id);
        $success = $stm->execute();
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        return ($success) ? $row : [];
    }
    public function readAll($table)
    {
        $stm = $this->pdo->prepare('SELECT * FROM ' . $table);
        $success = $stm->execute();
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
        return ($success) ? $rows : [];
    }
    public function readData($table, $id)
    {
        $stm = $this->pdo->prepare('SELECT * FROM ' . $table . ' WHERE `director_id` = (SELECT `id` FROM `director` WHERE `id` =:id)');
        $stm->bindValue(':id', $id);
        $success = $stm->execute();
        $row = $stm->fetchAll(PDO::FETCH_ASSOC);
        return ($success) ? $row : [];
    }

    //search data
    public function SearchData($search)
    {

        $stm = $this->pdo->prepare('SELECT * FROM `search_view` WHERE `description` LIKE :description
                              OR `category_name` LIKE :category_name
                              OR `type_name` LIKE :type_name');
        $search = "%" . $search . "%";

        $stm->bindParam(':description', $search);
        $stm->bindValue(':category_name', $search);
        $stm->bindValue(':type_name', $search);

        $success = $stm->execute();
        $row = $stm->fetchAll(PDO::FETCH_ASSOC);

        return ($success) ? $row : [];
    }
    //search data

    public function create($table, $data)
    {
        try {
            $columns = array_keys($data);
            $columnSql = implode(',', $columns);
            $bindingSql = ':' . implode(',:', $columns);
            $sql = "INSERT INTO $table ($columnSql) VALUES ($bindingSql)";
            $stm = $this->pdo->prepare($sql);
            foreach ($data as $key => $value) {
                $stm->bindValue(':' . $key, $value);
            }
            $status = $stm->execute();
            return ($status) ? $this->pdo->lastInsertId() : false;
        } catch (Exception $e) {
            echo ($e);
        }
    }
    public function update($table, $id, $data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $columns = array_keys($data);
        $columns = array_map(function ($item) {
            return $item . '=:' . $item;
        }, $columns);
        $bindingSql = implode(',', $columns);
        $sql = "UPDATE $table SET $bindingSql WHERE `id` = :id";
        $stm = $this->pdo->prepare($sql);
        $data['id'] = $id;
        foreach ($data as $key => $value) {
            $stm->bindValue(':' . $key, $value);
        }
        $status = $stm->execute();
        return $status;
    }
    /**
     * @param $table
     * @param $id
     * @return bool
     */
    public function delete($table, $id)
    {
        try {
        $stm = $this->pdo->prepare('DELETE FROM ' . $table . ' WHERE id = :id');
        $stm->bindParam(':id', $id);
        $success = $stm->execute();
        return ($success)? $success : '0';
    } catch (Exception $e) {
        // echo ($e);
    }
    }

    public function save($table, $data)
    {
        if (isset($data['id'])) {
            $this->update($table, $data['id'], $data);
        } else {
            return $this->create($table, $data);
        }
    }

    public function columnFilter($table, $column, $value)
    {

        $stm = $this->pdo->prepare('SELECT * FROM ' . $table . ' WHERE `' . str_replace('`', '', $column) . '` = :value');
        $stm->bindValue(':value', $value);
        $success = $stm->execute();
        $row = $stm->fetchAll(PDO::FETCH_ASSOC);

        return ($success) ? $row : [];
    }

    public function verify($id)
    {
        try {

            $sql = "UPDATE users SET `is_confirmed` =:true ,`is_active` ='1' WHERE `id` = $id";
            $stm = $this->pdo->prepare($sql);
            $stm->bindValue(':true', '1');
            $success = $stm->execute();
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            print_r($row);
            return ($success) ? $success : '0';
        } catch (Exception $e) {
            echo ($e);
        }
    }

    public function loginCheck($email, $password)
    {
        try {

            $sql = "SELECT * FROM users WHERE `email` =:email AND `password` =:password AND `is_confirmed` = '1'";
            $stm = $this->pdo->prepare($sql);
            $stm->bindValue(':email', $email);
            $stm->bindValue(':password', $password);
            $success = $stm->execute();
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            return ($success) ? $row : [];
        } catch (Exception $e) {
            echo ($e);
        }
    }

    public function setLogin($id)
    {
        try {

            $sql = "UPDATE users SET is_login = :true WHERE `id` = :id";
            $stm = $this->pdo->prepare($sql);
            $stm->bindValue(':true', '1');
            $stm->bindValue(':id', $id);
            $success = $stm->execute();
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            return ($success) ? $row : [];
        } catch (Exception $e) {
            echo ($e);
        }
    }

    public function unsetLogin($id)
    {
        try {

            $sql = "UPDATE users SET is_login = :false WHERE `id` = :id";
            $stm = $this->pdo->prepare($sql);
            $stm->bindValue(':false', '0');
            $stm->bindValue(':id', $id);
            $success = $stm->execute();
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            return ($success) ? $row : [];
        } catch (Exception $e) {
            echo ($e);
        }
    }

    public function incomeView()
    {
        try {

            $sql = "SELECT incomes.id,incomes.user_id,incomes.amount,incomes.date,categories.name AS category_name,incomes.category_id,users.name AS user_name
        FROM incomes
        LEFT JOIN categories ON incomes.category_id = categories.id
        LEFT JOIN users ON incomes.user_id = users.id";

            $stm = $this->pdo->prepare($sql);
            $success = $stm->execute();

            $row = $stm->fetchAll(PDO::FETCH_ASSOC);
            return ($success) ? $row : [];
        } catch (Exception $e) {
            echo ($e);
        }
    }

    public function expenseView()
    {
        try {

            $sql = "SELECT expenses.id,expenses.user_id,expenses.qty,expenses.amount,expenses.date,categories.name AS category_name,expenses.category_id,users.name AS user_name
        FROM expenses
        LEFT JOIN categories ON expenses.category_id = categories.id
        LEFT JOIN users ON expenses.user_id = users.id";

            $stm = $this->pdo->prepare($sql);
            $success = $stm->execute();

            $row = $stm->fetchAll(PDO::FETCH_ASSOC);
            return ($success) ? $row : [];
        } catch (Exception $e) {
            echo ($e);
        }
    }

    public function categoryView()
    {
        try {

            $sql = "SELECT categories.id,categories.name,categories.description,types.name AS type_name
        FROM categories
        LEFT JOIN types ON categories.type_id = types.id";

            $stm = $this->pdo->prepare($sql);
            $success = $stm->execute();

            $row = $stm->fetchAll(PDO::FETCH_ASSOC);
            return ($success) ? $row : [];
        } catch (Exception $e) {
            echo ($e);
        }
    }

    public function todayTransition($table)
    {
        try {

            $sql = "SELECT *,SUM(amount) AS amount FROM $table WHERE
        (date = { fn CURDATE() }) ";
            $stm = $this->pdo->prepare($sql);
            $success = $stm->execute();

            $row = $stm->fetch(PDO::FETCH_ASSOC);
            return ($success) ? $row : [];
        } catch (Exception $e) {
            echo ($e);
        }
    }

    public function expenseTransition($table)
    {
        try {

            $sql = "SELECT * ,SUM(amount*qty) AS amount FROM $table WHERE
        (date = { fn CURDATE() }) ";
            $stm = $this->pdo->prepare($sql);
            $success = $stm->execute();

            $row = $stm->fetch(PDO::FETCH_ASSOC);
            return ($success) ? $row : [];
        } catch (Exception $e) {
            echo ($e);
        }
    }
    public function getByCategoryId($table, $column)
    {
        $stm = $this->pdo->prepare('SELECT id FROM ' . $table . ' WHERE `name` = :column');
        $stm->bindValue(':column', $column);
        $success = $stm->execute();
        $row     = $stm->fetch(PDO::FETCH_ASSOC);
        return ($success) ? $row : [];
    }
}
