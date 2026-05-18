<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Contrato.php';

/**
 * Cuida de tudo que envolve contratos no banco de dados.
 *
 * O contrato é a entidade mais relacional do sistema — ele cruza três tabelas:
 * imoveis, clientes e corretores. A query de listagem usa três JOINs pra já
 * trazer os nomes de cada parte, porque mostrar só IDs na tela não serve pra ninguém.
 */
class ContratoDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    /**
     * Retorna todos os contratos com os nomes do imóvel, cliente e corretor.
     *
     * São três JOINs porque o contrato sozinho só tem IDs — pra mostrar nomes
     * na listagem, a gente precisa buscar cada entidade relacionada.
     */
    public function listar(): array
    {
        $sql = 'SELECT c.*, i.titulo AS imovel_titulo, cl.nome AS cliente_nome, co.nome AS corretor_nome
                FROM contratos c
                JOIN imoveis i    ON i.id  = c.id_imovel
                JOIN clientes cl  ON cl.id = c.id_cliente
                JOIN corretores co ON co.id = c.id_corretor
                ORDER BY c.id DESC';
        $stmt = $this->conn->query($sql);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    /**
     * Busca um contrato pelo ID. Se não achar, retorna null.
     */
    public function buscarPorId(int $id): ?Contrato
    {
        $stmt = $this->conn->prepare('SELECT * FROM contratos WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    /**
     * Salva um contrato no banco. Se tem ID, atualiza; se não tem, cria novo.
     *
     * A data_fim pode ser null — contratos de venda não têm prazo de término,
     * então deixar esse campo vazio é completamente válido.
     */
    public function salvar(Contrato $contrato): void
    {
        if ($contrato->getId()) {
            $stmt = $this->conn->prepare(
                'UPDATE contratos SET id_imovel = ?, id_cliente = ?, id_corretor = ?, tipo = ?, valor = ?, data_inicio = ?, data_fim = ? WHERE id = ?'
            );
            $stmt->execute([
                $contrato->getIdImovel(),
                $contrato->getIdCliente(),
                $contrato->getIdCorretor(),
                $contrato->getTipo(),
                $contrato->getValor(),
                $contrato->getDataInicio(),
                $contrato->getDataFim(),
                $contrato->getId(),
            ]);
        } else {
            $stmt = $this->conn->prepare(
                'INSERT INTO contratos (id_imovel, id_cliente, id_corretor, tipo, valor, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $contrato->getIdImovel(),
                $contrato->getIdCliente(),
                $contrato->getIdCorretor(),
                $contrato->getTipo(),
                $contrato->getValor(),
                $contrato->getDataInicio(),
                $contrato->getDataFim(),
            ]);
        }
    }

    /**
     * Remove um contrato pelo ID.
     */
    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM contratos WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Converte uma linha do banco em um objeto Contrato.
     *
     * Os campos imovel_titulo, cliente_nome e corretor_nome só aparecem
     * quando a query usou JOINs — por isso o isset() antes de preencher.
     */
    private function toModel(array $row): Contrato
    {
        $contrato = new Contrato();
        $contrato->setId((int) $row['id']);
        $contrato->setIdImovel((int) $row['id_imovel']);
        $contrato->setIdCliente((int) $row['id_cliente']);
        $contrato->setIdCorretor((int) $row['id_corretor']);
        $contrato->setTipo($row['tipo']);
        $contrato->setValor((float) $row['valor']);
        $contrato->setDataInicio($row['data_inicio']);
        $contrato->setDataFim($row['data_fim'] ?? null);
        if (isset($row['imovel_titulo'])) {
            $contrato->setImovelTitulo($row['imovel_titulo']);
            $contrato->setClienteNome($row['cliente_nome']);
            $contrato->setCorretorNome($row['corretor_nome']);
        }
        return $contrato;
    }
}
