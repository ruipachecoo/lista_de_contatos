// Endpoints do backend
const apiUrl = '/pessoas';
const contactApiUrl = '/contatos';

document.addEventListener('DOMContentLoaded', () => {
  // Referências aos elementos do HTML
  const form = document.getElementById('add-person-form');
  const peopleContainer = document.getElementById('people-container');

  /**
   * Função para sanitizar entradas de texto, prevenindo XSS comum
   * @param {string} str - Texto a ser sanitizado
   * @returns {string} Texto seguro para ser exibido
   */
  function sanitizeInput(str) {
    const temp = document.createElement('div');
    temp.textContent = str;
    return temp.innerHTML;
  }

  /**
   * 1) Busca todas as pessoas e seus contatos e exibe na tela
   */
  async function fetchPeople() {
    try {
      const response = await fetch(apiUrl);
      if (!response.ok) {
        throw new Error(`Erro na API: ${response.status}`);
      }
      const people = await response.json();

      // Limpa o container antes de preencher novamente
      peopleContainer.innerHTML = '';

      for (const person of people) {
        // Cria o bloco principal da pessoa
        const personDiv = document.createElement('div');
        personDiv.classList.add('person');
        personDiv.id = `person-${person.id}`;

        // Título com o nome da pessoa (sanitizado)
        const nameHeader = document.createElement('h2');
        nameHeader.textContent = sanitizeInput(person.nome);
        personDiv.appendChild(nameHeader);

        // Div para exibir contatos
        const contactList = document.createElement('div');
        contactList.className = 'contact-list';
        contactList.id = `contact-list-${person.id}`;
        personDiv.appendChild(contactList);

        // Div para ações (editar e excluir)
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'person-actions';

        // Botão de editar pessoa/contato
        const editButton = document.createElement('button');
        editButton.className = 'edit';
        editButton.textContent = 'Editar';
        editButton.onclick = () => {
          window.editContact(person.id);
        };
        actionsDiv.appendChild(editButton);

        // Botão de excluir
        const deleteButton = document.createElement('button');
        deleteButton.className = 'delete';
        deleteButton.textContent = 'Excluir';
        deleteButton.onclick = () => window.deletePerson(person.id);
        actionsDiv.appendChild(deleteButton);

        // Junta tudo no personDiv
        personDiv.appendChild(actionsDiv);
        peopleContainer.appendChild(personDiv);

        // Agora carrega os contatos daquela pessoa
        await fetchContacts(person.id);
      }
    } catch (error) {
      console.error('Erro ao buscar pessoas:', error);
    }
  }

  /**
   * 2) Carrega todos os contatos de uma pessoa e exibe na tela
   */
  async function fetchContacts(personId) {
    try {
      const response = await fetch(`${contactApiUrl}?pessoa_id=${encodeURIComponent(personId)}`);
      if (!response.ok) {
        throw new Error(`Erro na API: ${response.status}`);
      }
      const contacts = await response.json();

      // Seleciona o container correto para inserir os contatos
      const contactList = document.getElementById(`contact-list-${personId}`);
      if (!contactList) return;

      // Limpa antes de preencher
      contactList.innerHTML = '';

      for (const contact of contacts) {
        const contactDiv = document.createElement('div');
        contactDiv.className = 'contact';
        contactDiv.id = `contact-${contact.id}`;

        const contactInfoDiv = document.createElement('div');
        contactInfoDiv.className = 'contact-info';

        // Monta uma string exibindo somente os dados que existirem, todos sanitizados
        let contactText = '';
        if (contact.tipo) contactText += `<strong>${sanitizeInput(contact.tipo)}</strong> `;
        if (contact.valor) contactText += `${sanitizeInput(contact.valor)} `;
        if (contact.notas) contactText += `<em>${sanitizeInput(contact.notas)}</em>`;

        contactInfoDiv.innerHTML = contactText.trim();
        contactDiv.appendChild(contactInfoDiv);

        contactList.appendChild(contactDiv);
      }
    } catch (error) {
      console.error('Erro ao buscar contatos:', error);
    }
  }

  /**
   * 3) Lida com o envio do formulário (criação de pessoa e, opcionalmente, de contato)
   */
  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    // Captura os valores dos inputs
    const nameInput = sanitizeInput(document.getElementById('person-name').value.trim());
    const methodInput = sanitizeInput(document.getElementById('contact-method').value.trim());
    const infoInput = sanitizeInput(document.getElementById('contact-info').value.trim());
    const notesInput = sanitizeInput(document.getElementById('contact-notes').value.trim());

    if (!nameInput) {
      alert('Por favor, insira o nome da pessoa.');
      return;
    }

    try {
      // Cria a pessoa
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nome: nameInput }),
      });
      if (!response.ok) {
        throw new Error(`Erro ao criar pessoa: ${response.status}`);
      }
      const newPerson = await response.json();

      // Adiciona contato se necessário
      if (methodInput || infoInput || notesInput) {
        await fetch(contactApiUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            pessoa_id: newPerson.id,
            tipo: methodInput,
            valor: infoInput,
            notas: notesInput,
          }),
        });
      }

      // Atualiza a lista
      fetchPeople();
    } catch (error) {
      console.error('Erro ao adicionar pessoa/contato:', error);
    }
  });

  /**
   * 4) Exclui uma pessoa
   */
  window.deletePerson = async (personId) => {
    try {
      const response = await fetch(apiUrl, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: personId }),
      });
      if (!response.ok) {
        throw new Error(`Erro ao excluir pessoa: ${response.status}`);
      }
      fetchPeople();
    } catch (error) {
      console.error('Erro ao excluir pessoa:', error);
    }
  };

  /**
   * 5) Edita o contato de uma pessoa
   */
  window.editContact = async (personId) => {
    try {
      const response = await fetch(`${contactApiUrl}?pessoa_id=${encodeURIComponent(personId)}`);
      if (!response.ok) {
        throw new Error(`Erro ao buscar contatos: ${response.status}`);
      }
      const contacts = await response.json();

      if (!contacts || contacts.length === 0) {
        alert('Esta pessoa não tem contatos para editar.');
        return;
      }

      const contact = contacts[0];

      const typedTipo = prompt('Digite o novo meio de contato:', sanitizeInput(contact.tipo || ''));
      const typedValor = prompt('Digite a nova informação:', sanitizeInput(contact.valor || ''));
      const typedNotas = prompt('Digite as novas notas:', sanitizeInput(contact.notas || ''));

      const newTipo = (typedTipo === null || typedTipo.trim() === '') ? ' ' : sanitizeInput(typedTipo);
      const newValor = (typedValor === null || typedValor.trim() === '') ? ' ' : sanitizeInput(typedValor);
      const newNotas = (typedNotas === null || typedNotas.trim() === '') ? ' ' : sanitizeInput(typedNotas);

      const updateResponse = await fetch(`${contactApiUrl}?id=${contact.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ tipo: newTipo, valor: newValor, notas: newNotas }),
      });
      if (!updateResponse.ok) {
        throw new Error(`Erro ao atualizar contato: ${updateResponse.status}`);
      }

      fetchPeople();
    } catch (error) {
      console.error('Erro ao editar contato:', error);
    }
  };

  // Inicializa a lista de pessoas
  fetchPeople();
});

