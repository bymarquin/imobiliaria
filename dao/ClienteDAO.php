<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Cliente.php';

/**
 * Cuida de tudo que envolve clientes no banco de dados.
 *
 * DAO (Data Access Object) é o padrão usado aqui pra separar as queries SQL
 * do resto do sistema. Se a gente precisar trocar o banco algum dia, ou
 * ajustar uma query, mexe só aqui — nada mais quebra.
 *
 * Todas as queries usam prepare() + execute() em vez de montar SQL com strings.
 * Isso protege contra SQL Injection — impede que alguém mande código malicioso
 * dentro de um campo do formulário.
 */
class ClienteDAO
{
    private PDO $conn;

    public function __construct()
    {
        // Pega a conexão já aberta pelo Singleton — não cria uma nova
        $this->conn = Conexao::getConn();
    }

    /**
     * Retorna todos os clientes, do mais recente ao mais antigo.
     */
    public function listar(): array
    {
        $stmt = $this->conn->query('SELECT * FROM clientes ORDER BY id DESC');
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    /**
     * Busca um cliente pelo ID. Se não achar, retorna null — sem drama.
     */
    public function buscarPorId(int $id): ?Cliente
    {
        $stmt = $this->conn->prepare('SELECT * FROM clientes WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    /**
     * Salva um cliente no banco — cria novo ou atualiza existente.
     *
     * A decisão entre INSERT e UPDATE é simples: se o objeto já tem ID,
     * é uma edição. Se não tem, é um cadastro novo e o banco gera o ID.
     */
    public function salvar(Cliente $cliente): void
    {
        if ($cliente->getId()) {
            // Tem ID? Então é edição — atualiza o registro que já existe
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
            // Sem ID? É novo — o banco cria o ID automaticamente
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

    /**
     * Remove um cliente pelo ID.
     */
    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM clientes WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Converte uma linha do banco (array associativo) em um objeto Cliente.
     * É privado porque é detalhe interno deste DAO — quem chama listar()
     * não precisa saber como o banco organiza os dados.
     */
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
