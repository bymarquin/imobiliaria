<?php
require_once __DIR__ . '/../dao/VisitaDAO.php';
require_once __DIR__ . '/../dao/ImovelDAO.php';

// Recebe os dados do formulário, organiza e repassa pro DAO salvar ou buscar.
// Também usa o ImovelDAO pra preencher o select de imóveis no formulário.
class VisitaController
{
    private VisitaDAO $dao;
    private ImovelDAO $imovelDAO;

    public function __construct()
    {
        $this->dao       = new VisitaDAO();
        $this->imovelDAO = new ImovelDAO();
    }

    // Retorna todas as visitas agendadas
    public function listar(): array
    {
        return $this->dao->listar();
    }

    // Busca uma visita pelo ID pra preencher o formulário de edição
    public function buscarPorId(int $id): ?Visita
    {
        return $this->dao->buscarPorId($id);
    }

    // Retorna todos os imóveis pra preencher o select no formulário
    public function listarImoveis(): array
    {
        return $this->imovelDAO->listar();
    }

    // Recebe os dados do formulário e salva a visita
    public function salvar(array $data): void
    {
        $visita  = new Visita();
        $horario = trim($data['horario_preferencia'] ?? '');

        if (!empty($data['id'])) $visita->setId((int) $data['id']); // se tem ID é edição
        $visita->setIdImovel((int) ($data['id_imovel'] ?? 0));
        $visita->setNome(trim($data['nome'] ?? ''));
        $visita->setEmail(trim($data['email'] ?? ''));
        $visita->setCelular(trim($data['celular'] ?? ''));
        $visita->setDiaSemana($data['dia_semana'] ?? 'segunda');
        $visita->setHorarioPreferencia($horario);
        $visita->setPeriodo($this->periodoPorHorario($horario)); // calcula o período com base no horário
        $this->dao->salvar($visita);
    }

    // Remove a visita pelo ID
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }

    // Converte o horário HH:MM em período do dia.
    // Manhã: até 11h59 | Tarde: 12h às 17h59 | Noite: 18h em diante
    private function periodoPorHorario(string $horario): string
    {
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $horario)) {
            return 'manha'; // formato inválido cai em manhã
        }

        $hora = (int) substr($horario, 0, 2);

        if ($hora < 12) return 'manha';
        if ($hora < 18) return 'tarde';
        return 'noite';
    }
}
