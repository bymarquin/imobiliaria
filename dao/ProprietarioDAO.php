<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Proprietario.php';

class ProprietarioDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    public function listar(): array
    {
        $stmt = $this->conn->query('SELECT * FROM proprietarios ORDER BY id DESC');
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    public function buscarPorId(int $id): ?Proprietario
    {
        $stmt = $this->conn->prepare('SELECT * FROM proprietarios WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    public function salvar(Proprietario $proprietario): void
    {
        if ($proprietario->getId()) {
            $stmt = $this->conn->prepare(
                'UPDATE proprietarios SET nome = ?, cpf = ?, telefone = ?, email = ? WHERE id = ?'
            );
            $stmt->execute([
                $proprietario->getNome(),
                $proprietario->getCpf(),
                $proprietario->getTelefone(),
                $proprietario->getEmail(),
                $proprietario->getId(),
            ]);
        } else {
            $stmt = $this->conn->prepare(
                'INSERT INTO proprietarios (nome, cpf, telefone, email) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $proprietario->getNome(),
                $proprietario->getCpf(),
                $proprietario->getTelefone(),
                $proprietario->getEmail(),
            ]);
        }
    }

    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM proprietarios WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function toModel(array $row): Proprietario
    {
        $proprietario = new Proprietario();
        $proprietario->setId((int) $row['id']);
        $proprietario->setNome($row['nome']);
        $proprietario->setCpf($row['cpf']);
        $proprietario->setTelefone($row['telefone'] ?? '');
        $proprietario->setEmail($row['email'] ?? '');
        return $proprietario;
    }
}
