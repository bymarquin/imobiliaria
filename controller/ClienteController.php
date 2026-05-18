<?php
require_once __DIR__ . '/../dao/ClienteDAO.php';

/**
 * Intermediário entre o formulário HTML e o banco de dados.
 *
 * No MVC, o Controller é quem recebe os dados brutos do $_POST, organiza
 * tudo num objeto limpo e manda pro DAO salvar. Assim a View não precisa
 * saber nada sobre o banco, e o DAO não precisa lidar com dados crus
 * vindos direto do usuário.
 *
 * Parece uma camada a mais, mas faz muita diferença quando o sistema cresce.
 */
class ClienteController
{
    private ClienteDAO $dao;

    public function __construct()
    {
        $this->dao = new ClienteDAO();
    }

    /**
     * Retorna todos os clientes pra exibição na listagem.
     */
    public function listar(): array
    {
        return $this->dao->listar();
    }

    /**
     * Busca um cliente pelo ID pra pré-preencher o formulário de edição.
     */
    public function buscarPorId(int $id): ?Cliente
    {
        return $this->dao->buscarPorId($id);
    }

    /**
     * Recebe os dados do formulário, monta um objeto Cliente e salva.
     *
     * O trim() tira espaços em branco acidentais que o usuário pode ter
     * deixado nos campos de texto — acontece mais do que a gente imagina.
     */
    public function salvar(array $data): void
    {
        $cliente = new Cliente();
        // Se tem ID no formulário é edição, se não tem é cadastro novo
        if (!empty($data['id'])) $cliente->setId((int) $data['id']);
        $cliente->setNome(trim($data['nome'] ?? ''));
        $cliente->setCpf(trim($data['cpf'] ?? ''));
        $cliente->setTelefone(trim($data['telefone'] ?? ''));
        $cliente->setEmail(trim($data['email'] ?? ''));
        $cliente->setInteresse($data['interesse'] ?? 'compra');
        $this->dao->salvar($cliente);
    }

    /**
     * Manda o DAO remover o cliente com o ID informado.
     */
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
