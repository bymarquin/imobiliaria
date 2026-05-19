<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Cliente.php';

// Responsável por todas as operações de banco de dados relacionadas a clientes.
class ClienteDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    // Retorna todos os clientes
    public function listar(): array
    {
        $stmt = $this->conn->query('SELECT * FROM clientes ORDER BY id DESC');
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    // Busca um cliente pelo ID; retorna null se não encontrar
    public function buscarPorId(int $id): ?Cliente
    {
        $stmt = $this->conn->prepare('SELECT * FROM clientes WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    // Salva o cliente: se tiver ID atualiza, se não tiver cria novo
    public function salvar(Cliente $cliente): void
    {
        if ($cliente->getId()) {
            $stmt = $this->conn->prepare(
                'UPDATE clientes SET nome = ?, cpf = ?, telefone = ?, email = ?, interesse = ? WHERE id = ?'
            );
            $stmt->execute([
                $cliente->getNome(),
                $cliente->getCpf(),
                $cliente->getTelefone(),
                $cliente->getEmail(),
                $cliente->getInteresse(),
                $cliente->getId(),
            ]);
        } else {
            $stmt = $this->conn->prepare(
                'INSERT INTO clientes (nome, cpf, telefone, email, interesse) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $cliente->getNome(),
                $cliente->getCpf(),
                $cliente->getTelefone(),
                $cliente->getEmail(),
                $cliente->getInteresse(),
            ]);
        }
    }

    // Remove o cliente do banco pelo ID
    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM clientes WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Converte uma linha do banco em um objeto Cliente
    private function toModel(array $row): Cliente
    {
        $cliente = new Cliente();
        $cliente->setId((int) $row['id']);
        $cliente->setNome($row['nome']);
        $cliente->setCpf($row['cpf']);
        $cliente->setTelefone($row['telefone'] ?? '');
        $cliente->setEmail($row['email'] ?? '');
        $cliente->setInteresse($row['interesse']);
        return $cliente;
    }
}
