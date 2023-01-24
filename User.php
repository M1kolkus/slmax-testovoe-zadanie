<?php

class User
{
    private ?int $id;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $birthday;
    private ?int $sex;
    private ?string $cityOfBirth;

    public function __construct(
        ?int   $id,
        string $firstName = null,
        string $lastName = null,
        string $birthday = null,
        int    $sex = null,
        string $cityOfBirth = null
    )
    {
        if ($id === null) {
            $error = [];
            preg_match("/^[a-zA-Zа-яёА-ЯЁ]+$/u", $firstName) === 1 ? $this->firstName = $firstName : $error['firstName'] = 'error';
            preg_match("/^[a-zA-Zа-яёА-ЯЁ]+$/u", $lastName) === 1 ? $this->lastName = $lastName : $error['lastName'] = 'error';
            preg_match("/[0-9\-]/", $birthday) === 1 ? $this->birthday = $birthday : $error['birthday'] = 'error';
            $sex === 0 || $sex === 1 ? $this->sex = $sex : $error['sex'] = 'error';
            preg_match("/^[a-zA-Zа-яёА-ЯЁ]+$/u", $cityOfBirth) === 1 ? $this->cityOfBirth = $cityOfBirth : $error['city of birthday'] = 'error';

            if (!empty($error)) {
                return $error;
            }

            $this->save();
            $this->id = $this->pdo()->lastInsertId();
        } else {
            $sth = $this->pdo()->prepare("SELECT * FROM users WHERE id = :id");
            $sth->execute([':id' => $id]);
            $user = $sth->fetchObject();

            $this->id = $user->id;
            $this->lastName = $user->last_name;
            $this->firstName = $user->first_name;
            $this->birthday = $user->birthday;
            $this->sex = $user->sex;
            $this->cityOfBirth = $user->city_of_birth;
        }
    }

    private function pdo(): PDO
    {
        return new PDO(
            "mysql:host=127.0.0.1;dbname=slmax_testovoe_zadanie",
            'root',
            'root1234',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    private function save(): void
    {
        $sql = 'INSERT INTO users SET first_name=:firstName, last_name=:lastName, birthday=:birthday, sex=:sex, city_of_birth=:cityOfBirth';
        $params = [
            ':firstName' => $this->firstName,
            ':lastName' => $this->lastName,
            ':birthday' => $this->birthday,
            ':sex' => $this->sex,
            ':cityOfBirth' => $this->cityOfBirth
        ];

        $sth = $this->pdo()->prepare($sql);
        $sth->execute($params);
    }

    public function delete(): void
    {
        $sth = $this->pdo()->prepare("DELETE FROM users WHERE id = :id");
        $sth->execute([':id' => $this->id]);
    }

    public static function getAge(string $birthday): int
    {
        $date1 = new DateTime($birthday);
        $date2 = new DateTime();

        return $date1->diff($date2)->format("%y");
    }

    public static function getSex(int $numb): string
    {
        return $numb === 0 ? 'муж' : 'жен';
    }

    public function getUser(): stdClass
    {
        $user = new stdClass();
        $user->id = $this->id;
        $user->firstName = $this->firstName;
        $user->lastName = $this->lastName;
        $user->birthday = self::getAge($this->birthday);
        $user->sex = self::getSex($this->sex);
        $user->cityOfBirth = $this->cityOfBirth;

        return $user;
    }
}
