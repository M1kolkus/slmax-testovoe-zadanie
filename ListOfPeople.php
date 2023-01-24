<?php

include_once __DIR__ . "/User.php";

class ListOfPeople
{
    private array $arrayId = [];

    public function __construct(int $id, string $sign = '=')
    {
        if (!class_exists('User')) {
            return 'class User does not exist';
        }

        $validSigns = ['>', '<', '!=', '='];

        if (!in_array($sign, $validSigns)) {
            return "{$sign} is not valid";
        }

        $sth = $this->pdo()->prepare("SELECT id FROM users WHERE id {$sign} {$id}");
        $sth->execute();
        $arrayObject = $sth->fetchAll(PDO::FETCH_CLASS);

        foreach ($arrayObject as $object) {
            $this->arrayId[] = $object->id;
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

    public function getUserList(): array
    {
        $userList = [];
        foreach ($this->arrayId as $id) {
            $userList[] = new User($id);
        }

        return $userList;
    }

    public function deleteById(): void
    {
        $userList = $this->getUserList();
        foreach ($userList as $user) {
            $user->delete();
        }
    }
}
