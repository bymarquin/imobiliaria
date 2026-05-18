<?php
require_once __DIR__ . '/../dao/ImovelDAO.php';
require_once __DIR__ . '/../dao/ProprietarioDAO.php';

/**
 * Intermediário entre o formulário de imóveis e o banco de dados.
 *
 * Além do CRUD normal, esse controller expõe listarProprietarios() — necessário
 * pra preencher o campo select do formulário, onde o usuário escolhe o dono do imóvel.
 * Por isso ele mantém dois DAOs: o de imóvel e o de proprietário.
 */
class ImovelController
{
    private ImovelDAO $dao;
    private ProprietarioDAO $proprietarioDAO;

    public function __construct()
    {
        $this->dao = new ImovelDAO();
        // O ProprietarioDAO é necessário pra popular o dropdown no formulário
        $this->proprietarioDAO = new ProprietarioDAO();
    }

    /**
     * Retorna imóveis com o nome do proprietário embutido.
     * Aceita um filtro opcional de finalidade ("venda" ou "aluguel").
     */
    public function listar(string $finalidade = ''): array
    {
        return $this->dao->listar($finalidade);
    }

    /**
     * Busca um imóvel pelo ID pra pré-preencher o formulário de edição.
     */
    public function buscarPorId(int $id): ?Imovel
    {
        return $this->dao->buscarPorId($id);
    }

    /**
     * Retorna todos os proprietários — usado pra preencher o select no formulário.
     */
    public function listarProprietarios(): array
    {
        return $this->proprietarioDAO->listar();
    }

    /**
     * Recebe os dados do formulário, cuida do upload da planta baixa e salva.
     *
     * A planta baixa é opcional — se o usuário não enviar nenhum arquivo,
     * mantemos o que já estava salvo (campo oculto "planta_baixa_atual").
     */
    public function salvar(array $data): void
    {
        $imovel = new Imovel();
        if (!empty($data['id'])) $imovel->setId((int) $data['id']);
        $imovel->setTitulo(trim($data['titulo'] ?? ''));
        $imovel->setTipo($data['tipo'] ?? 'casa');
        $imovel->setEndereco(trim($data['endereco'] ?? ''));
        $imovel->setValor((float) ($data['valor'] ?? 0));
        $imovel->setStatus($data['status'] ?? 'disponivel');
        $imovel->setFinalidade($data['finalidade'] ?? 'venda');
        $imovel->setMetrosQuadrados(!empty($data['metros_quadrados']) ? (float) $data['metros_quadrados'] : null);
        $imovel->setIdProprietario((int) ($data['id_proprietario'] ?? 0));

        // Verifica se veio um arquivo de planta baixa no upload
        if (!empty($_FILES['planta_baixa']['tmp_name'])) {
            $ext      = strtolower(pathinfo($_FILES['planta_baixa']['name'], PATHINFO_EXTENSION));
            $filename = 'planta_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['planta_baixa']['tmp_name'], __DIR__ . '/../uploads/' . $filename);
            $imovel->setPlantaBaixa($filename);
        } else {
            // Edição sem novo upload: mantém o arquivo que já existia
            $imovel->setPlantaBaixa(!empty($data['planta_baixa_atual']) ? $data['planta_baixa_atual'] : null);
        }

        $this->dao->salvar($imovel);
    }

    /**
     * Remove o imóvel com o ID informado do banco.
     */
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
