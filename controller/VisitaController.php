<?php
// Carrega o DAO de visitas (salvar, listar, excluir visita).
require_once __DIR__ . '/../dao/VisitaDAO.php';
// Carrega o DAO de imoveis (para montar o select de imoveis).
require_once __DIR__ . '/../dao/ImovelDAO.php';

// Controller = camada que organiza o fluxo da tela.
// Ele recebe dados da requisicao, prepara o model e chama o DAO.
class VisitaController
{
    // DAO principal de visitas.
    private VisitaDAO $dao;
    // DAO de imoveis para consultas de apoio.
    private ImovelDAO $imovelDAO;

    // Construtor: cria os DAOs usados pelo controller.
    public function __construct()
    {
        $this->dao       = new VisitaDAO();
        $this->imovelDAO = new ImovelDAO();
    }

    // Devolve todas as visitas para listagem.
    public function listar(): array
    {
        return $this->dao->listar();
    }

    // Busca uma visita por ID para preencher formulario de edicao.
    public function buscarPorId(int $id): ?Visita
    {
        return $this->dao->buscarPorId($id);
    }

    // Devolve todos os imoveis para montar o select da tela.
    public function listarImoveis(): array
    {
        return $this->imovelDAO->listar();
    }

    // Recebe os dados do formulario e salva no banco.
    public function salvar(array $data): void
    {
        // Cria o model vazio.
        $visita  = new Visita();
        // Pega o horario e remove espacos extras.
        $horario = trim($data['horario_preferencia'] ?? '');

        // Se veio ID, significa edicao (nao criacao).
        if (!empty($data['id'])) $visita->setId((int) $data['id']); // se tem ID é edição

        // Copia cada campo do formulario para o model.
        $visita->setIdImovel((int) ($data['id_imovel'] ?? 0));
        $visita->setNome(trim($data['nome'] ?? ''));
        $visita->setEmail(trim($data['email'] ?? ''));
        $visita->setCelular(trim($data['celular'] ?? ''));
        $visita->setDiaSemana($data['dia_semana'] ?? 'segunda');
        $visita->setHorarioPreferencia($horario);

        // Antes de salvar, verifica se o horario ja esta ocupado para o mesmo imovel.
        // Em edicao, ignora o proprio registro para evitar conflito falso.
        $idAtual = $visita->getId();
        $conflito = $this->dao->existeConflitoHorario(
            $visita->getIdImovel(),
            $visita->getDiaSemana(),
            $visita->getHorarioPreferencia(),
            $idAtual
        );

        if ($conflito) {
            throw new RuntimeException('Este horario ja esta agendado para o imovel selecionado. Escolha outro horario.');
        }

        // Regra simples do sistema:
        // com base no horario, define manha/tarde/noite automaticamente.
        $visita->setPeriodo($this->periodoPorHorario($horario)); // calcula o período com base no horário

        // Envia para o DAO persistir no banco.
        $this->dao->salvar($visita);
    }

    // Exclui visita pelo ID.
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }

    // Regra utilitaria:
    // converte HH:MM para periodo do dia.
    // Manha: 00:00 ate 11:59
    // Tarde: 12:00 ate 17:59
    // Noite: 18:00 ate 23:59
    private function periodoPorHorario(string $horario): string
    {
        // Valida formato HH:MM.
        // Se vier fora do formato, cai em "manha" por padrao.
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $horario)) {
            return 'manha'; // formato inválido cai em manhã
        }

        // Extrai so a hora (primeiros 2 caracteres) e converte para numero.
        $hora = (int) substr($horario, 0, 2);

        // Decide o periodo com base na hora.
        if ($hora < 12) return 'manha';
        if ($hora < 18) return 'tarde';
        return 'noite';
    }
}
