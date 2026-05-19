<?php
require_once __DIR__ . '/../dao/ImovelDAO.php';
require_once __DIR__ . '/../dao/ProprietarioDAO.php';

// Recebe os dados do formulário, organiza e repassa pro DAO salvar ou buscar.
// Também usa o ProprietarioDAO pra preencher o select de proprietários no formulário.
class ImovelController
{
    private ImovelDAO $dao;
    private ProprietarioDAO $proprietarioDAO;

    public function __construct()
    {
        $this->dao = new ImovelDAO();
        $this->proprietarioDAO = new ProprietarioDAO();
    }

    // Retorna os imóveis; aceita filtro de finalidade ("venda" ou "aluguel")
    public function listar(string $finalidade = ''): array
    {
        return $this->dao->listar($finalidade);
    }

    // Busca um imóvel pelo ID pra preencher o formulário de edição
    public function buscarPorId(int $id): ?Imovel
    {
        return $this->dao->buscarPorId($id);
    }

    // Retorna todos os proprietários pra preencher o select no formulário
    public function listarProprietarios(): array
    {
        return $this->proprietarioDAO->listar();
    }

    // Recebe os dados do formulário, cuida do upload da planta e salva o imóvel
    public function salvar(array $data): void
    {
        $imovel = new Imovel();
        if (!empty($data['id'])) $imovel->setId((int) $data['id']); // se tem ID é edição
        $imovel->setTitulo(trim($data['titulo'] ?? ''));
        $imovel->setTipo($data['tipo'] ?? 'casa');
        $imovel->setEndereco(trim($data['endereco'] ?? ''));
        $imovel->setValor((float) ($data['valor'] ?? 0));
        $imovel->setStatus($data['status'] ?? 'disponivel');
        $imovel->setFinalidade($data['finalidade'] ?? 'venda');
        $imovel->setMetrosQuadrados(!empty($data['metros_quadrados']) ? (float) $data['metros_quadrados'] : null);
        $imovel->setIdProprietario((int) ($data['id_proprietario'] ?? 0));

        // Se veio um arquivo de planta baixa, salva na pasta uploads/
        if (!empty($_FILES['planta_baixa']['tmp_name'])) {
            $ext      = strtolower(pathinfo($_FILES['planta_baixa']['name'], PATHINFO_EXTENSION));
            $filename = 'planta_' . uniqid() . '.' . $ext; // nome único pra evitar conflito
            move_uploaded_file($_FILES['planta_baixa']['tmp_name'], __DIR__ . '/../uploads/' . $filename);
            $imovel->setPlantaBaixa($filename);
        } else {
            // Sem novo arquivo: mantém a planta que já estava salva
            $imovel->setPlantaBaixa(!empty($data['planta_baixa_atual']) ? $data['planta_baixa_atual'] : null);
        }

        $this->dao->salvar($imovel);
    }

    // Remove o imóvel pelo ID
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
