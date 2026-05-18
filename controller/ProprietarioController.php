<?php
require_once __DIR__ . '/../dao/ProprietarioDAO.php';

/**
 * Intermediário entre o formulário de proprietários e o banco de dados.
 *
 * Recebe os dados da View, monta o objeto Proprietario e delega pro DAO
 * salvar ou buscar. Mesma responsabilidade dos outros controllers.
 */
class ProprietarioController
{
    private ProprietarioDAO $dao;

    public function __construct()
    {
        $this->dao = new ProprietarioDAO();
    }

    /**
     * Retorna todos os proprietários pra listagem.
     */
    public function listar(): array
    {
        return $this->dao->listar();
    }

    /**
     * Busca um proprietário pelo ID pra pré-preencher o formulário de edição.
     */
    public function buscarPorId(int $id): ?Proprietario
    {
        return $this->dao->buscarPorId($id);
    }

    /**
     * Recebe os dados do formulário, monta o objeto Proprietario e salva.
     */
    public function salvar(array $data): void
    {
        $proprietario = new Proprietario();
        if (!empty($data['id'])) $proprietario->setId((int) $data['id']);
        $proprietario->setNome(trim($data['nome'] ?? ''));
        $proprietario->setCpf(trim($data['cpf'] ?? ''));
        $proprietario->setTelefone(trim($data['telefone'] ?? ''));
        $proprietario->setEmail(trim($data['email'] ?? ''));
        $this->dao->salvar($proprietario);
    }

    /**
     * Remove o proprietário com o ID informado.
     */
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
