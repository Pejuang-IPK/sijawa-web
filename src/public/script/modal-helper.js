

const PembantuModal = {

    buatOverlay(zIndex = 1000) {
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.style.cssText = `position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: ${zIndex};`;
        return overlay;
    },

    buatTombolTutup(onClick) {
        const btn = document.createElement('button');
        btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        btn.style.cssText = 'background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 18px;';
        btn.onclick = onClick;
        return btn;
    },

    buatHeader(title, onClose) {
        const header = document.createElement('div');
        header.style.cssText = 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;';
        
        const titleEl = document.createElement('h3');
        titleEl.textContent = title;
        titleEl.style.margin = '0';
        
        const closeBtn = this.buatTombolTutup(onClose);
        
        header.appendChild(titleEl);
        header.appendChild(closeBtn);
        return header;
    },

    buatInputForm(label, id, value, type = 'text', required = true) {
        const container = document.createElement('div');
        container.style.marginBottom = '20px';
        
        const labelEl = document.createElement('label');
        labelEl.textContent = label;
        labelEl.style.cssText = 'display: block; margin-bottom: 8px; font-weight: 500; color: #334155;';
        
        const input = type === 'select' 
            ? document.createElement('select')
            : document.createElement('input');
        
        if (type !== 'select') input.type = type;
        if (value !== undefined) input.value = value;
        if (required) input.required = true;
        input.style.cssText = 'width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;';
        
        container.appendChild(labelEl);
        container.appendChild(input);
        return container;
    },

    buatGrupTombol(buttons) {
        const group = document.createElement('div');
        group.style.cssText = 'display: flex; gap: 12px; margin-top: 24px;';
        
        buttons.forEach(btn => {
            const button = document.createElement('button');
            button.textContent = btn.text;
            button.onclick = btn.onClick;
            button.style.cssText = btn.style || 'padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; background: #e2e8f0;';
            group.appendChild(button);
        });
        
        return group;
    },

    formatTanggal(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
    },

    formatRupiah(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    }
};

