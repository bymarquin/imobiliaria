<?php
require_once __DIR__ . '/../dao/ContratoDAO.php';
require_once __DIR__ . '/../dao/ImovelDAO.php';
require_once __DIR__ . '/../dao/ClienteDAO.php';
require_once __DIR__ . '/../dao/CorretorDAO.php';

// Recebe os dados do formulário, organiza e repassa pro DAO salvar ou buscar.
// Usa três DAOs extras só pra preencher os selects do formulário.
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

    // Retorna todos os contratos com nomes do imóvel, cliente e corretor
    public function listar(): array
    {
        return $this->dao->listar();
    }

    // Busca um contrato pelo ID pra preencher o formulário de edição
    public function buscarPorId(int $id): ?Contrato
    {
        return $this->dao->buscarPorId($id);
    }

    // Os três métodos abaixo preenchem os selects no formulário de contrato
    public function listarImoveis(): array    { return $this->imovelDAO->listar(); }
    public function listarClientes(): array   { return $this->clienteDAO->listar(); }
    public function listarCorretores(): array { return $this->corretorDAO->listar(); }

    // Recebe os dados do formulário e salva o contrato
    public function salvar(array $data): void
    {
        $idContrato = !empty($data['id']) ? (int) $data['id'] : 0;
        $idImovel = (int) ($data['id_imovel'] ?? 0);
        $tipo = $data['tipo'] ?? 'venda';

        $imovel = $this->imovelDAO->buscarPorId($idImovel);
        if (!$imovel) {
            throw new RuntimeException('Imovel invalido para contrato.');
        }

        if ($idContrato === 0 && $imovel->getStatus() !== 'disponivel') {
            throw new RuntimeException('Este imovel nao esta disponivel para novo contrato.');
        }

        $contrato = new Contrato();
        if ($idContrato > 0) $contrato->setId($idContrato); // se tem ID é edição
        $contrato->setIdImovel($idImovel);
        $contrato->setIdCliente((int) ($data['id_cliente'] ?? 0));
        $contrato->setIdCorretor((int) ($data['id_corretor'] ?? 0));
        $contrato->setTipo($tipo);
        $contrato->setValor((float) ($data['valor'] ?? 0));
        $contrato->setDataInicio($data['data_inicio'] ?? '');
        $contrato->setDataFim(!empty($data['data_fim']) ? $data['data_fim'] : null); // data_fim é opcional
        $this->dao->salvar($contrato);

        // Contrato fechado deve refletir no status operacional do imóvel.
        $novoStatus = $tipo === 'aluguel' ? 'alugado' : 'vendido';
        $this->imovelDAO->atualizarStatus($idImovel, $novoStatus);
    }

    // Remove o contrato pelo ID
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
