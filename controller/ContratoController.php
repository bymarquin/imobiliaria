<?php
require_once __DIR__ . '/../dao/ContratoDAO.php';
require_once __DIR__ . '/../dao/ImovelDAO.php';
require_once __DIR__ . '/../dao/ClienteDAO.php';
require_once __DIR__ . '/../dao/CorretorDAO.php';

/**
 * Intermediário entre o formulário de contratos e o banco de dados.
 *
 * É o controller mais cheio do sistema — o contrato relaciona três entidades,
 * então a gente precisa de quatro DAOs aqui: ContratoDAO, ImovelDAO, ClienteDAO
 * e CorretorDAO. Os três últimos são só pra popular os selects do formulário.
 */
class ContratoController
{
    private ContratoDAO $dao;
    private ImovelDAO $imovelDAO;
    private ClienteDAO $clienteDAO;
    private CorretorDAO $corretorDAO;

    public function __construct()
    {
        $this->dao         = new ContratoDAO();
        $this->imovelDAO   = new ImovelDAO();
        $this->clienteDAO  = new ClienteDAO();
        $this->corretorDAO = new CorretorDAO();
    }

    /**
     * Retorna todos os contratos com os nomes do imóvel, cliente e corretor já preenchidos.
     */
    public function listar(): array
    {
        return $this->dao->listar();
    }

    /**
     * Busca um contrato pelo ID pra pré-preencher o formulário de edição.
     */
    public function buscarPorId(int $id): ?Contrato
    {
        return $this->dao->buscarPorId($id);
    }

    // Esses três métodos populam os selects no formulário de contrato

    /** Imóveis disponíveis pra selecionar no formulário. */
    public function listarImoveis(): array    { return $this->imovelDAO->listar(); }

    /** Clientes disponíveis pra selecionar no formulário. */
    public function listarClientes(): array   { return $this->clienteDAO->listar(); }

    /** Corretores disponíveis pra selecionar no formulário. */
    public function listarCorretores(): array { return $this->corretorDAO->listar(); }

    /**
     * Recebe os dados do formulário, monta o objeto Contrato e salva.
     *
     * A data_fim é opcional — se vier vazia, guarda null. Faz sentido:
     * contratos de venda não têm prazo de término.
     */
    public function salvar(array $data): void
    {
        $contrato = new Contrato();
        if (!empty($data['id'])) $contrato->setId((int) $data['id']);
        $contrato->setIdImovel((int) ($data['id_imovel'] ?? 0));
        $contrato->setIdCliente((int) ($data['id_cliente'] ?? 0));
        $contrato->setIdCorretor((int) ($data['id_corretor'] ?? 0));
        $contrato->setTipo($data['tipo'] ?? 'venda');
        $contrato->setValor((float) ($data['valor'] ?? 0));
        $contrato->setDataInicio($data['data_inicio'] ?? '');
        // Se a data_fim vier vazia do formulário, guarda null — campo opcional
        $contrato->setDataFim(!empty($data['data_fim']) ? $data['data_fim'] : null);
        $this->dao->salvar($contrato);
    }

    /**
     * Remove o contrato com o ID informado do banco.
     */
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
