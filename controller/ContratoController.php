<?php
require_once __DIR__ . '/../dao/ContratoDAO.php';
require_once __DIR__ . '/../dao/ImovelDAO.php';
require_once __DIR__ . '/../dao/ClienteDAO.php';
require_once __DIR__ . '/../dao/CorretorDAO.php';

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

    public function listar(): array
    {
        return $this->dao->listar();
    }

    public function buscarPorId(int $id): ?Contrato
    {
        return $this->dao->buscarPorId($id);
    }

    public function listarImoveis(): array    { return $this->imovelDAO->listar(); }
    public function listarClientes(): array   { return $this->clienteDAO->listar(); }
    public function listarCorretores(): array { return $this->corretorDAO->listar(); }

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

        if ($tipo === 'aluguel' && empty($data['data_fim'])) {
            throw new RuntimeException('Contratos de aluguel exigem uma data de fim.');
        }

        if ($tipo === 'aluguel' && $imovel->getFinalidade() !== 'aluguel') {
            throw new RuntimeException('Este imovel nao esta disponivel para aluguel.');
        }

        if ($tipo === 'venda' && $imovel->getFinalidade() !== 'venda') {
            throw new RuntimeException('Este imovel nao esta disponivel para venda.');
        }

        $contrato = new Contrato();
        if ($idContrato > 0) $contrato->setId($idContrato);
        $contrato->setIdImovel($idImovel);
        $contrato->setIdCliente((int) ($data['id_cliente'] ?? 0));
        $contrato->setIdCorretor((int) ($data['id_corretor'] ?? 0));
        $contrato->setTipo($tipo);
        $contrato->setValor((float) ($data['valor'] ?? 0));
        $contrato->setDataInicio($data['data_inicio'] ?? '');
        $contrato->setDataFim(!empty($data['data_fim']) ? $data['data_fim'] : null);
        $contrato->setStatus('ativo');
        $this->dao->salvar($contrato);

        $novoStatus = $tipo === 'aluguel' ? 'alugado' : 'vendido';
        $this->imovelDAO->atualizarStatus($idImovel, $novoStatus);
    }

    public function encerrar(int $id): void
    {
        $contrato = $this->dao->buscarPorId($id);
        if (!$contrato) {
            throw new RuntimeException('Contrato nao encontrado.');
        }
        if ($contrato->getStatus() !== 'ativo') {
            throw new RuntimeException('Este contrato ja esta encerrado.');
        }

        $this->dao->encerrar($id);

        if ($contrato->getTipo() === 'aluguel') {
            $this->imovelDAO->atualizarStatus($contrato->getIdImovel(), 'disponivel');
        }
    }

    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
