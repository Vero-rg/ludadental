export type AppointmentStatus =
    | 'pendiente'
    | 'confirmada'
    | 'cancelada'
    | 'completada';

export type PatientType = 'adulto' | 'nino';

export type AppointmentRecord = {
    id: number;
    codigo_folio: string;
    paciente_nombre: string;
    paciente_telefono: string;
    paciente_email: string | null;
    servicio: string;
    fecha: string;
    hora: string;
    tipo_paciente: PatientType;
    notas: string | null;
    estado: AppointmentStatus;
    created_at?: string | null;
};

export type OccupiedSlot = {
    fecha: string;
    hora: string;
    id: number;
};

export type TreatmentResultRecord = {
    id: number;
    titulo: string;
    descripcion: string;
    servicio: string | null;
    imagen_antes: string;
    imagen_despues: string;
    visible: boolean;
    orden: number;
    created_at?: string | null;
};

export type WeeklySlots = Record<number, string[]>;

export type ClinicPhone = {
    label: string;
    number: string;
};

export type ClinicInfo = {
    name: string;
    tagline: string;
    timezone: string;
    whatsapp: string;
    phones: ClinicPhone[];
    address: {
        line1: string;
        line2: string;
        reference: string;
    };
    map: {
        embed_url: string;
        link: string;
    };
};

export type OpeningHour = {
    days: string;
    time: string;
    open: boolean;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginator<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
};
