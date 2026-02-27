

import { parse, isValid, differenceInYears } from 'date-fns';

export const validateAge = (v, t = (s) => s) => {
    if (!v) return true;

    let birthDate;

    if (v instanceof Date) {
        birthDate = v;
    } else if (typeof v === 'string') {
        
        if (/^\d{4}-\d{2}-\d{2}$/.test(v)) {
            birthDate = new Date(v + 'T00:00:00');
        }
        
        else if (/^\d{2}\/\d{2}\/\d{4}$/.test(v)) {
            const [day, month, year] = v.split('/').map(Number);
            birthDate = new Date(year, month - 1, day);
        }
        
        else {
            birthDate = new Date(v);
        }
    }

    if (!birthDate || !isValid(birthDate)) return false;

    const today = new Date();
    const age = differenceInYears(today, birthDate);

    return age >= 18 || t('validation.age_restriction') || 'Você deve ter pelo menos 18 anos';
};

export const validateCPF = (v, t = (s) => s) => {
    if (!v) return true;
    const cpf = v.replace(/\D/g, '');

    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
        return t('validation.cpf_invalid') || 'CPF inválido';
    }

    let sum = 0;
    let remainder;

    for (let i = 1; i <= 9; i++) {
        sum = sum + parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }

    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(9, 10))) {
        return t('validation.cpf_invalid') || 'CPF inválido';
    }

    sum = 0;
    for (let i = 1; i <= 10; i++) {
        sum = sum + parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }

    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(10, 11))) {
        return t('validation.cpf_invalid') || 'CPF inválido';
    }

    return true;
};
