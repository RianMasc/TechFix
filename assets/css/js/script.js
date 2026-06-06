/**
 * Se o usuário clicar em Cancelar, o evento é interrompido imediatamente.
 */
function confirmarExclusao(event, entidade) {
    var resposta = window.confirm("Atenção! Você tem certeza absoluta que deseja remover este " + entidade + "?\nEsta ação apagará os dados permanentemente.");
    
    if (!resposta) {
        event.preventDefault(); // Cancela o clique e a navegação
        return false;
    }
    return true; //  continua a exclusão
}

// Inicialização de componentes visuais do Bootstrap
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});