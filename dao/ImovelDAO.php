<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Imovel.php';

class ImovelDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    public function listar(string $finalidade = ''): array
    {
        $sql = 'SELECT i.*, p.nome AS nome_proprietario
                FROM imoveis i
                JOIN proprietarios p ON p.id = i.id_proprietario';

        $params = [];
        if ($finalidade === 'venda' || $finalidade === 'aluguel') {
            $sql .= ' WHERE i.finalidade = ?';
            $params[] = $finalidade;
        }

        $sql .= ' ORDER BY i.id DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    public function buscarPorId(int $id): ?Imovel
    {
        $stmt = $this->conn->prepare('SELECT * FROM imoveis WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    public function salvar(Imovel $imovel): void
    {
        if ($imovel->getId()) {
            $stmt = $this->conn->prepare(
                'UPDATE imoveis SET titulo = ?, tipo = ?, endereco = ?, valor = ?, status = ?, finalidade = ?, metros_quadrados = ?, planta_baixa = ?, id_proprietario = ? WHERE id = ?'
            );
            $stmt->execute([
                $imovel->getTitulo(),
                $imovel->getTipo(),
                $imovel->getEndereco(),
                $imovel->getValor(),
                $imovel->getStatus(),
                $imovel->getFinalidade(),
                $imovel->getMetrosQuadrados(),
                $imovel->getPlantaBaixa(),
                $imovel->getIdProprietario(),
                $imovel->getId(),
            ]);
        } else {
            $stmt = $this->conn->prepare(
                'INSERT INTO imoveis (titulo, tipo, endereco, valor, status, finalidade, metros_quadrados, planta_baixa, id_proprietario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $imovel->getTitulo(),
                $imovel->getTipo(),
                $imovel->getEndereco(),
                $imovel->getValor(),
                $imovel->getStatus(),
                $imovel->getFinalidade(),
                $imovel->getMetrosQuadrados(),
                $imovel->getPlantaBaixa(),
                $imovel->getIdProprietario(),
            ]);
        }
    }

    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM imoveis WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function atualizarStatus(int $id, string $status): void
    {
        $stmt = $this->conn->prepare('UPDATE imoveis SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    private function toModel(array $row): Imovel
    {
        $imovel = new Imovel();
        $imovel->setId((int) $row['id']);
        $imovel->setTitulo($row['titulo']);
        $imovel->setTipo($row['tipo']);
        $imovel->setEndereco($row['endereco']);
        $imovel->setValor((float) $row['valor']);
        $imovel->setStatus($row['status']);
        $imovel->setFinalidade($row['finalidade']);
        $imovel->setIdProprietario((int) $row['id_proprietario']);
        $imovel->setMetrosQuadrados(isset($row['metros_quadrados']) && $row['metros_quadrados'] !== null ? (float) $row['metros_quadrados'] : null);
        $imovel->setPlantaBaixa($row['planta_baixa'] ?? null);
        if (isset($row['nome_proprietario'])) {
            $imovel->setNomeProprietario($row['nome_proprietario']);
        }
        return $imovel;
    }
}
