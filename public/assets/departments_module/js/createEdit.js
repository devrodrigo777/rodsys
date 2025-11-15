async function generateDescriptionWithGemini(departmentName, onError) {
    try {
            const response = await fetch(window.BURL + 'dashboard/departamentos/api/generate-description', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                departmentName: departmentName
            })
        });

        if (!response.ok) {
            console.error('Erro ao chamar Gemini API:', response.statusText);

            if(onError) {
                onError();
            }
            return;
        }

        const data = await response.json();
        const description = data.content.trim();
        
        // Ensure description doesn't exceed 40 characters
        const truncatedDescription = description.substring(0, 40);
        
        $('#inputDescricao').val(truncatedDescription);

    } catch (error) {
        console.error('Erro ao gerar descrição:', error);

        if (onError) {
            onError();
        }

        const inputDescricao = document.getElementById('inputDescricao');
        inputDescricao.disabled = false;
        inputDescricao.value = '';
        inputDescricao.focus();
    }
}

// Auto-generate description on focus
$(document).ready(function() {
    let tryAI = true;
    $('#inputDescricao').on('focus', function() {
        if(tryAI){
            tryAI = false;
            const nome = $('#inputNome').val().trim();
            
            // Only proceed if nome is not empty and description is empty
            if (nome && !$(this).val().trim()) {
                $(this).prop('disabled', true); // Disable input while generating
                $(this).val('Gerando descrição...'); // Optional: show a message while generating

                generateDescriptionWithGemini(nome, () =>{
                    const inputDescricao = document.getElementById('inputDescricao');
                    inputDescricao.disabled = false;
                    inputDescricao.value = '';
                    inputDescricao.focus();
                });
            }
        }
    });
});
