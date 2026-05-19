<?php
require_once __DIR__ . '/../dao/ImovelDAO.php';
require_once __DIR__ . '/../dao/ProprietarioDAO.php';

class ImovelController
{
    private ImovelDAO $dao;
    private ProprietarioDAO $proprietarioDAO;

    public function __construct()
    {
        $this->dao = new ImovelDAO();
        $this->proprietarioDAO = new ProprietarioDAO();
    }

    public function listar(string $finalidade = ''): array
    {
        return $this->dao->listar($finalidade);
    }

    public function buscarPorId(int $id): ?Imovel
    {
        return $this->dao->buscarPorId($id);
    }

    public function listarProprietarios(): array
    {
        return $this->proprietarioDAO->listar();
    }

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

        if (!empty($_FILES['planta_baixa']['tmp_name'])) {
            $ext      = strtolower(pathinfo($_FILES['planta_baixa']['name'], PATHINFO_EXTENSION));
            $filename = 'planta_' . uniqid() . '.' . $ext;
            $uploadsDir = __DIR__ . '/../uploads';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0775, true);
            }
            if (move_uploaded_file($_FILES['planta_baixa']['tmp_name'], $uploadsDir . '/' . $filename)) {
                $imovel->setPlantaBaixa($filename);
            } else {
                $imovel->setPlantaBaixa(!empty($data['planta_baixa_atual']) ? $data['planta_baixa_atual'] : null);
            }
        } else {
            $imovel->setPlantaBaixa(!empty($data['planta_baixa_atual']) ? $data['planta_baixa_atual'] : null);
        }

        $this->dao->salvar($imovel);
    }

    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
