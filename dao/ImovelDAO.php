<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Imovel.php';

/**
 * Cuida de tudo que envolve imóveis no banco de dados.
 *
 * Tem um detalhe diferente dos outros DAOs: a listagem usa um JOIN com a
 * tabela de proprietários pra já trazer o nome do dono junto. Assim a gente
 * não precisa de uma segunda consulta só pra mostrar esse dado na tela.
 */
class ImovelDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    /**
     * Retorna todos os imóveis com o nome do proprietário já embutido.
     * Aceita um filtro opcional de finalidade ("venda" ou "aluguel").
     */
    public function listar(string $finalidade = ''): array
    {
        $sql = 'SELECT i.*, p.nome AS nome_proprietario
                FROM imoveis i
                JOIN proprietarios p ON p.id = i.id_proprietario';

        if ($finalidade === 'venda' || $finalidade === 'aluguel') {
            $sql .= " WHERE i.finalidade = '$finalidade'";
        }

        $sql .= ' ORDER BY i.id DESC';
        $stmt = $this->conn->query($sql);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    /**
     * Busca um imóvel pelo ID. Retorna null se não achar.
     * Essa query simples não traz o nome do proprietário — só os dados do imóvel.
     */
    public function buscarPorId(int $id): ?Imovel
    {
        $stmt = $this->conn->prepare('SELECT * FROM imoveis WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    /**
     * Salva um imóvel no banco. Se tem ID, atualiza; se não tem, cria novo.
     */
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

    /**
     * Remove um imóvel pelo ID.
     */
    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM imoveis WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Converte uma linha do banco em um objeto Imovel.
     *
     * O nome_proprietario só aparece quando a query usou JOIN, então
     * checamos com isset() antes de tentar preenchê-lo.
     */
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
