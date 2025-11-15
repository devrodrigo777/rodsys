async function generateDescriptionWithGemini(departmentName, onError) {
    try {
        const apiKey = 'AIzaSyAV4ytwqm1yR0zV1t3JwCI7dJJkF8OMyuM'; // Make sure to set this in your view
        const prompt = `Gere uma descrição concisa e profissional para um departamento chamado "${departmentName}". A descrição deve ter no máximo 40 caracteres. Responda apenas com a descrição, sem explicações adicionais.Ex: "Departamento de Recursos Humanos".`;
        
        const response = await fetch('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', {
            method: 'POST',
            headers: {
                'x-goog-api-key': apiKey,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                contents: [{
                    parts: [{
                        text: prompt
                    }]
                }]
            })
        });

        if (!response.ok) {
            console.error('Erro ao chamar Gemini API:', response.statusText);
            return;
        }

        const data = await response.json();
        const description = data.candidates[0].content.parts[0].text.trim();
        
        // Ensure description doesn't exceed 40 characters
        const truncatedDescription = description.substring(0, 40);
        
        $('#inputDescricao').val(truncatedDescription);
        $("#inputDescricao").prop('disabled', false); // Re-enable input after generation
        $("#inputDescricao").focus();

    } catch (error) {
        console.error('Erro ao gerar descrição:', error);

        if (onError) {
            onError();
        }
    }
}

// Auto-generate description on focus
$(document).ready(function() {
    
    $('#inputDescricao').on('focus', function() {
        const nome = $('#inputNome').val().trim();
        
        // Only proceed if nome is not empty and description is empty
        if (nome && !$(this).val().trim()) {
            $(this).prop('disabled', true); // Disable input while generating
            $(this).val('Gerando descrição...'); // Optional: show a message while generating

            generateDescriptionWithGemini(nome, () =>{
                $('#inputDescricao').prop('disabled', false); // Re-enable input after generation
                ("#inputDescricao").val("");
            });
        }
    });
});
