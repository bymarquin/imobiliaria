<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Proprietario.php';

/**
 * Cuida de tudo que envolve proprietários no banco de dados.
 *
 * Mesma estrutura dos outros DAOs: acesso ao banco isolado aqui, prepared
 * statements pra segurança, e toModel() pra transformar linha do banco em objeto.
 */
class ProprietarioDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    /**
     * Retorna todos os proprietários, do mais recente ao mais antigo.
     */
    public function listar(): array
    {
        $stmt = $this->conn->query('SELECT * FROM proprietarios ORDER BY id DESC');
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    /**
     * Busca um proprietário pelo ID. Se não encontrar, retorna null.
     */
    public function buscarPorId(int $id): ?Proprietario
    {
        $stmt = $this->conn->prepare('SELECT * FROM proprietarios WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    /**
     * Salva um proprietário no banco. Se tem ID, atualiza; se não tem, cria novo.
     */
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

    /**
     * Remove um proprietário do banco pelo ID.
     */
    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM proprietarios WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Converte uma linha do banco em um objeto Proprietario.
     */
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
