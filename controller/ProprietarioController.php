<?php
require_once __DIR__ . '/../dao/ProprietarioDAO.php';

// Recebe os dados do formulário, organiza e repassa pro DAO salvar ou buscar.
class ProprietarioController
{
    private ProprietarioDAO $dao;

    public function __construct()
    {
        $this->dao = new ProprietarioDAO();
    }

    // Retorna todos os proprietários
    public function listar(): array
    {
        return $this->dao->listar();
    }

    // Busca um proprietário pelo ID pra preencher o formulário de edição
    public function buscarPorId(int $id): ?Proprietario
    {
        return $this->dao->buscarPorId($id);
    }

    // Recebe os dados do formulário e salva o proprietário
    public function salvar(array $data): void
    {
        $proprietario = new Proprietario();
        if (!empty($data['id'])) $proprietario->setId((int) $data['id']); // se tem ID é edição
        $proprietario->setNome(trim($data['nome'] ?? ''));
        $proprietario->setCpf(trim($data['cpf'] ?? ''));
        $proprietario->setTelefone(trim($data['telefone'] ?? ''));
        $proprietario->setEmail(trim($data['email'] ?? ''));
        $this->dao->salvar($proprietario);
    }

    // Remove o proprietário pelo ID
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
