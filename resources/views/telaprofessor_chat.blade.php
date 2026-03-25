<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Professor - Chat</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", sans-serif; background: #f3f7ff; color: #0b2447; }
        header { background: linear-gradient(45deg, #002168, #3676ff); color: #fff; padding: 18px 24px; }
        main { padding: 24px; }
        .teacher-nav { display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
        .teacher-link { text-decoration: none; padding: 10px 14px; border-radius: 8px; background: #e7eefc; color: #0b2447; font-weight: 600; }
        .teacher-link.active { background: #0b2447; color: #fff; }

        .chat-shell { max-width: 1120px; background: #fff; border-radius: 12px; box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08); overflow: hidden; }
        .chat-body { display: grid; grid-template-columns: 300px 1fr; min-height: 540px; }

        .contacts { border-right: 1px solid #e8edf7; padding: 16px; background: #fbfcff; }
        .contacts h4 { margin: 0 0 12px; }
        .contact-search {
            width: 100%;
            margin-bottom: 10px;
            border: 1px solid #bfd1ff;
            border-radius: 8px;
            padding: 9px 10px;
            font-size: 14px;
        }
        .contact-list { display: flex; flex-direction: column; gap: 8px; }
        .contact-btn {
            text-align: left;
            width: 100%;
            border: 1px solid #dbe6ff;
            border-radius: 10px;
            background: #fff;
            padding: 10px 12px;
            padding-right: 28px;
            color: #0b2447;
            cursor: pointer;
            font-size: 14px;
            position: relative;
        }
        .contact-btn.active { background: #0b2447; border-color: #0b2447; color: #fff; }
        .contact-btn.has-unread::after {
            content: "";
            position: absolute;
            top: 10px;
            right: 10px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #00A86B;
            box-shadow: 0 0 0 2px #fff;
        }
        .contact-email { font-weight: 600; display: block; }
        .contact-name { font-weight: 700; display: block; }
        .contact-role { font-size: 12px; opacity: .85; }

        .conversation { display: flex; flex-direction: column; }
        .conversation-head { padding: 14px 18px; border-bottom: 1px solid #e8edf7; font-weight: 700; }
        .messages {
            flex: 1;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            background: #fff;
        }
        .msg {
            max-width: 76%;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.3;
            background: #0b2447;
            color: #fff;
            border: 1px solid #0b2447;
        }
        .msg.mine { align-self: flex-end; }
        .msg.other { align-self: flex-start; }
        .msg small { display: block; margin-top: 6px; opacity: .75; font-size: 11px; }

        .composer { border-top: 1px solid #e8edf7; padding: 12px; display: flex; gap: 10px; }
        .composer input {
            flex: 1;
            border: 1px solid #bfd1ff;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
        }
        .composer button {
            border: none;
            border-radius: 8px;
            background: #3676ff;
            color: #fff;
            padding: 0 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .notice { padding: 16px; color: #5f6d88; }

        @media (max-width: 900px) {
            .chat-body { grid-template-columns: 1fr; }
            .contacts { border-right: none; border-bottom: 1px solid #e8edf7; }
        }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('telaprofessor') }}"><img src="{{ asset('img/jardim-logotipo122.png') }}" alt="Imagem" width="120px" height="70px" class="logo"></a>
        <h3>Ambiente do Professor - Chat</h3>
    </header>
    <main>
        <nav class="teacher-nav">
            <a class="teacher-link" href="{{ route('telaprofessor.turma') }}">Turma</a>
            <a class="teacher-link" href="{{ route('telaprofessor.notificacoes') }}">Notificacoes</a>
            <a class="teacher-link active" href="{{ route('telaprofessor.chat') }}">Chat</a>
            <a class="teacher-link" href="{{ route('telaprofessor.perfil') }}">Perfil</a>
            <a href="{{ route('logout') }}" class="teacher-link">Sair</a>
        </nav>

        <section class="chat-shell">
            <div class="chat-body">
                <aside class="contacts">
                    <h4>Contatos</h4>
                    <input type="text" id="contact-search" class="contact-search" placeholder="Pesquisar por nome...">
                    @if ($contatos->isEmpty())
                        <p class="notice">Nao ha outros usuarios cadastrados na tabela de acesso.</p>
                    @else
                        <div class="contact-list" id="contact-list">
                            @foreach ($contatos as $contato)
                                <button
                                    type="button"
                                    class="contact-btn"
                                    data-email="{{ $contato->email }}"
                                    data-nome="{{ mb_strtolower((string) data_get($contato, 'nome', '')) }}"
                                    data-role="{{ $contato->tipo }}"
                                >
                                    <span class="contact-name">{{ data_get($contato, 'nome', data_get($contato, 'email')) }}</span>
                                    <span class="contact-email">{{ $contato->email }}</span>
                                    <span class="contact-role">{{ ucfirst($contato->tipo) }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </aside>

                <div class="conversation">
                    <div class="conversation-head" id="conversation-head">Selecione um contato</div>
                    <div class="messages" id="messages"></div>
                    <form class="composer" id="composer" style="display:none;">
                        <input type="text" id="message-input" maxlength="1000" placeholder="Digite sua mensagem..." required>
                        <button type="submit">Enviar</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script>
        const contatos = @json($contatos->values());
        const meuEmail = @json($meuEmail);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const contactList = document.getElementById('contact-list');
        const head = document.getElementById('conversation-head');
        const messagesBox = document.getElementById('messages');
        const composer = document.getElementById('composer');
        const input = document.getElementById('message-input');
        const contactSearch = document.getElementById('contact-search');

        let contatoAtual = null;
        let pollTimer = null;
        let unreadPollTimer = null;
        const baselineState = new Map();
        const unreadState = new Map();

        function buildMessageKey(msg) {
            return [
                msg.remetente_email || '',
                msg.destinatario_email || '',
                msg.created_at || '',
                msg.mensagem || '',
            ].join('|');
        }

        function aplicarIndicadorNaoLido(email) {
            if (!contactList) return;
            const btn = Array.from(contactList.querySelectorAll('.contact-btn'))
                .find((item) => item.dataset.email === email);
            if (!btn) return;
            btn.classList.toggle('has-unread', !!unreadState.get(email));
        }

        function limparNotificacao(email) {
            unreadState.set(email, false);
            aplicarIndicadorNaoLido(email);
        }

        function marcarNotificacao(email) {
            if (contatoAtual && contatoAtual.email === email) return;
            unreadState.set(email, true);
            aplicarIndicadorNaoLido(email);
        }

        function formatTime(ts) {
            const d = new Date(ts);
            if (Number.isNaN(d.getTime())) return '';
            return d.toLocaleString('pt-PT', { hour12: false });
        }

        function renderMensagens(items) {
            messagesBox.innerHTML = '';

            if (!items.length) {
                messagesBox.innerHTML = '<p class="notice">Sem mensagens ainda. Inicie a conversa.</p>';
                return;
            }

            for (const msg of items) {
                const mine = msg.remetente_email === meuEmail;
                const node = document.createElement('div');
                node.className = `msg ${mine ? 'mine' : 'other'}`;

                const from = mine ? 'Voce' : msg.remetente_email;
                node.innerHTML = `${msg.mensagem}<small>${from} - ${formatTime(msg.created_at)}</small>`;
                messagesBox.appendChild(node);
            }

            messagesBox.scrollTop = messagesBox.scrollHeight;
        }

        async function buscarMensagens(email) {
            const url = `{{ route('chat.fetch') }}?contato=${encodeURIComponent(email)}`;
            const resp = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });

            if (!resp.ok) return null;
            return await resp.json();
        }

        async function carregarMensagens() {
            if (!contatoAtual) return;

            const data = await buscarMensagens(contatoAtual.email);
            if (!data) return;
            const mensagens = data.mensagens || [];
            renderMensagens(mensagens);

            const ultima = mensagens.length ? mensagens[mensagens.length - 1] : null;
            baselineState.set(contatoAtual.email, ultima ? buildMessageKey(ultima) : '');
            limparNotificacao(contatoAtual.email);
        }

        async function atualizarIndicadoresNaoLidos() {
            for (const contato of contatos) {
                const emailContato = (contato.email || '').toString();
                if (!emailContato) continue;
                if (contatoAtual && contatoAtual.email === emailContato) continue;

                const data = await buscarMensagens(emailContato);
                if (!data) continue;

                const mensagens = data.mensagens || [];
                const ultima = mensagens.length ? mensagens[mensagens.length - 1] : null;
                const ultimaKey = ultima ? buildMessageKey(ultima) : '';
                const baseline = baselineState.has(emailContato) ? baselineState.get(emailContato) : null;

                if (baseline === null) {
                    baselineState.set(emailContato, ultimaKey);
                    continue;
                }

                if (baseline !== ultimaKey) {
                    baselineState.set(emailContato, ultimaKey);
                    if (ultima && (ultima.remetente_email || '') === emailContato) {
                        marcarNotificacao(emailContato);
                    }
                }
            }
        }

        async function enviarMensagem(texto) {
            const resp = await fetch(`{{ route('chat.send') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    destinatario_email: contatoAtual.email,
                    mensagem: texto,
                }),
            });

            if (!resp.ok) {
                let erro = 'Falhou ao enviar a mensagem.';
                try {
                    const body = await resp.json();
                    if (body?.message) erro = body.message;
                } catch (e) {}
                alert(erro);
                return false;
            }

            input.value = '';
            await carregarMensagens();
            return true;
        }

        function selecionarContato(email) {
            contatoAtual = contatos.find(c => c.email === email) || null;
            if (!contatoAtual) return;

            if (contactList) {
                const buttons = contactList.querySelectorAll('.contact-btn');
                buttons.forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.email === email);
                });
            }

            head.textContent = `Conversa com ${contatoAtual.email}`;
            composer.style.display = 'flex';
            limparNotificacao(email);
            carregarMensagens();

            if (pollTimer) clearInterval(pollTimer);
            pollTimer = setInterval(carregarMensagens, 4000);
        }

        if (contactList) {
            contactList.addEventListener('click', (event) => {
                const btn = event.target.closest('.contact-btn');
                if (!btn) return;
                selecionarContato(btn.dataset.email);
            });
        }

        if (contactSearch && contactList) {
            contactSearch.addEventListener('input', () => {
                const termo = contactSearch.value.trim().toLowerCase();
                contactList.querySelectorAll('.contact-btn').forEach((btn) => {
                    const email = (btn.dataset.email || '').toLowerCase();
                    const nome = (btn.dataset.nome || '').toLowerCase();
                    const mostrar = termo === '' || nome.includes(termo) || email.includes(termo);
                    btn.style.display = mostrar ? '' : 'none';
                });
            });
        }

        composer.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!contatoAtual) return;

            const texto = input.value.trim();
            if (!texto) return;
            await enviarMensagem(texto);
        });

        if (contatos.length > 0) {
            contatos.forEach((contato) => {
                const emailContato = (contato.email || '').toString();
                if (!emailContato) return;
                unreadState.set(emailContato, false);
                aplicarIndicadorNaoLido(emailContato);
            });
            selecionarContato(contatos[0].email);
        }

        if (unreadPollTimer) clearInterval(unreadPollTimer);
        unreadPollTimer = setInterval(atualizarIndicadoresNaoLidos, 5000);
    </script>
</body>
</html>
