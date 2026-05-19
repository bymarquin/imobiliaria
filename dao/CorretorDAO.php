<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Corretor.php';

class CorretorDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    public function listar(): array
    {
        $stmt = $this->conn->query('SELECT * FROM corretores ORDER BY id DESC');
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    public function buscarPorId(int $id): ?Corretor
    {
        $stmt = $this->conn->prepare('SELECT * FROM corretores WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    public function salvar(Corretor $corretor): void
    {
        if ($corretor->getId()) {
            $stmt = $this->conn->prepare(
                'UPDATE corretores SET nome = ?, creci = ?, telefone = ?, email = ? WHERE id = ?'
            );
            $stmt->execute([
                $corretor->getNome(),
                $corretor->getCreci(),
                $corretor->getTelefone(),
                $corretor->getEmail(),
                $corretor->getId(),
            ]);
        } else {
            $stmt = $this->conn->prepare(
                'INSERT INTO corretores (nome, creci, telefone, email) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $corretor->getNome(),
                $corretor->getCreci(),
                $corretor->getTelefone(),
                $corretor->getEmail(),
            ]);
        }
    }

    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM corretores WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function toModel(array $row): Corretor
    {
        $corretor = new Corretor();
        $corretor->setId((int) $row['id']);
        $corretor->setNome($row['nome']);
        $corretor->setCreci($row['creci']);
        $corretor->setTelefone($row['telefone'] ?? '');
        $corretor->setEmail($row['email'] ?? '');
        return $corretor;
    }
}
