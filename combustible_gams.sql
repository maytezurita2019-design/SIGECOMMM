--
-- PostgreSQL database dump
--

\restrict DVHGTLajKHfyTYg63CaHzqCAadXQztsbl09yFl2UOcUKIUUlKKf7ygLGyGOQWiL

-- Dumped from database version 17.10
-- Dumped by pg_dump version 17.10

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: aperturas_programaticas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.aperturas_programaticas (
    id bigint NOT NULL,
    codigo character varying(50) NOT NULL,
    descripcion character varying(255),
    fuente_financiamiento_id bigint NOT NULL,
    organismo_financiador_id bigint NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.aperturas_programaticas OWNER TO postgres;

--
-- Name: aperturas_programaticas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.aperturas_programaticas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.aperturas_programaticas_id_seq OWNER TO postgres;

--
-- Name: aperturas_programaticas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.aperturas_programaticas_id_seq OWNED BY public.aperturas_programaticas.id;


--
-- Name: auditorias; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.auditorias (
    id bigint NOT NULL,
    usuario_id bigint,
    accion character varying(50) NOT NULL,
    tabla character varying(100) NOT NULL,
    registro_id bigint NOT NULL,
    datos_anteriores json,
    datos_nuevos json,
    ip character varying(45),
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.auditorias OWNER TO postgres;

--
-- Name: auditorias_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.auditorias_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auditorias_id_seq OWNER TO postgres;

--
-- Name: auditorias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.auditorias_id_seq OWNED BY public.auditorias.id;


--
-- Name: entregas_policia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.entregas_policia (
    id bigint NOT NULL,
    lote_vale_id bigint NOT NULL,
    contrato character varying(150) NOT NULL,
    dependencia character varying(150) NOT NULL,
    recibido_por character varying(150) NOT NULL,
    fecha_entrega date NOT NULL,
    cantidad integer NOT NULL,
    numero_inicial bigint NOT NULL,
    numero_final bigint NOT NULL,
    solicitud_token uuid NOT NULL,
    usuario_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.entregas_policia OWNER TO postgres;

--
-- Name: entregas_policia_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.entregas_policia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.entregas_policia_id_seq OWNER TO postgres;

--
-- Name: entregas_policia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.entregas_policia_id_seq OWNED BY public.entregas_policia.id;


--
-- Name: estaciones_servicio; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.estaciones_servicio (
    id bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    direccion character varying(255),
    telefono character varying(30),
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.estaciones_servicio OWNER TO postgres;

--
-- Name: estaciones_servicio_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.estaciones_servicio_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.estaciones_servicio_id_seq OWNER TO postgres;

--
-- Name: estaciones_servicio_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.estaciones_servicio_id_seq OWNED BY public.estaciones_servicio.id;


--
-- Name: fuentes_financiamiento; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fuentes_financiamiento (
    id bigint NOT NULL,
    codigo character varying(20) NOT NULL,
    nombre character varying(150) NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.fuentes_financiamiento OWNER TO postgres;

--
-- Name: fuentes_financiamiento_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.fuentes_financiamiento_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.fuentes_financiamiento_id_seq OWNER TO postgres;

--
-- Name: fuentes_financiamiento_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.fuentes_financiamiento_id_seq OWNED BY public.fuentes_financiamiento.id;


--
-- Name: lotes_vales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lotes_vales (
    id bigint NOT NULL,
    numero_inicial bigint NOT NULL,
    numero_final bigint NOT NULL,
    fecha_recepcion date NOT NULL,
    periodo_id bigint NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.lotes_vales OWNER TO postgres;

--
-- Name: lotes_vales_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lotes_vales_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lotes_vales_id_seq OWNER TO postgres;

--
-- Name: lotes_vales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lotes_vales_id_seq OWNED BY public.lotes_vales.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: organismos_financiadores; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.organismos_financiadores (
    id bigint NOT NULL,
    codigo character varying(20) NOT NULL,
    nombre character varying(150) NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.organismos_financiadores OWNER TO postgres;

--
-- Name: organismos_financiadores_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.organismos_financiadores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.organismos_financiadores_id_seq OWNER TO postgres;

--
-- Name: organismos_financiadores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.organismos_financiadores_id_seq OWNED BY public.organismos_financiadores.id;


--
-- Name: partes_diarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.partes_diarios (
    id bigint NOT NULL,
    vehiculo_id bigint NOT NULL,
    fecha date NOT NULL,
    presentado boolean DEFAULT false NOT NULL,
    observacion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.partes_diarios OWNER TO postgres;

--
-- Name: partes_diarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.partes_diarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.partes_diarios_id_seq OWNER TO postgres;

--
-- Name: partes_diarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.partes_diarios_id_seq OWNED BY public.partes_diarios.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: periodos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.periodos (
    id bigint NOT NULL,
    mes smallint NOT NULL,
    anio smallint NOT NULL,
    fecha_inicio date NOT NULL,
    fecha_fin date NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.periodos OWNER TO postgres;

--
-- Name: periodos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.periodos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.periodos_id_seq OWNER TO postgres;

--
-- Name: periodos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.periodos_id_seq OWNED BY public.periodos.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    nombre character varying(50) NOT NULL,
    descripcion character varying(255),
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: series_vales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.series_vales (
    id bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    numero_inicial bigint DEFAULT '1'::bigint NOT NULL,
    ultimo_numero bigint DEFAULT '0'::bigint NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    motivo text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.series_vales OWNER TO postgres;

--
-- Name: series_vales_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.series_vales_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.series_vales_id_seq OWNER TO postgres;

--
-- Name: series_vales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.series_vales_id_seq OWNED BY public.series_vales.id;


--
-- Name: solicitudes_contrasena; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.solicitudes_contrasena (
    id bigint NOT NULL,
    usuario_id bigint NOT NULL,
    estado character varying(20) DEFAULT 'pendiente'::character varying NOT NULL,
    fecha_atencion timestamp(0) without time zone,
    atendido_por bigint,
    ip_solicitud character varying(45),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.solicitudes_contrasena OWNER TO postgres;

--
-- Name: solicitudes_contrasena_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.solicitudes_contrasena_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.solicitudes_contrasena_id_seq OWNER TO postgres;

--
-- Name: solicitudes_contrasena_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.solicitudes_contrasena_id_seq OWNED BY public.solicitudes_contrasena.id;


--
-- Name: tipos_combustible; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tipos_combustible (
    id bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    unidad_medida character varying(30) DEFAULT 'Litros'::character varying NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tipos_combustible OWNER TO postgres;

--
-- Name: tipos_combustible_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tipos_combustible_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tipos_combustible_id_seq OWNER TO postgres;

--
-- Name: tipos_combustible_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tipos_combustible_id_seq OWNED BY public.tipos_combustible.id;


--
-- Name: unidades; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.unidades (
    id bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    sigla character varying(30),
    tipo character varying(50),
    descripcion character varying(255),
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.unidades OWNER TO postgres;

--
-- Name: unidades_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.unidades_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.unidades_id_seq OWNER TO postgres;

--
-- Name: unidades_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.unidades_id_seq OWNED BY public.unidades.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    id bigint NOT NULL,
    nombre character varying(100),
    apellido character varying(100),
    ci character varying(30),
    correo character varying(150) NOT NULL,
    contrasena character varying(255) NOT NULL,
    rol_id bigint NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: vales_combustible; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vales_combustible (
    id bigint NOT NULL,
    numero_vale bigint NOT NULL,
    fecha date,
    lote_vale_id bigint,
    vehiculo_id bigint,
    apertura_programatica_id bigint NOT NULL,
    usuario_id bigint NOT NULL,
    numero_factura character varying(50),
    cantidad_litros numeric(21,13),
    precio_unitario numeric(12,4),
    total numeric(14,2),
    kilometraje character varying(50),
    observacion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    estacion_servicio_id bigint,
    tipo_combustible_id bigint,
    periodo_id bigint,
    estado character varying(20) DEFAULT 'EMITIDO'::character varying NOT NULL,
    motivo_anulacion text,
    fecha_anulacion timestamp(0) without time zone,
    documento_respaldo character varying(255),
    anio smallint NOT NULL,
    entrega_policia_id bigint,
    serie_vale_id bigint
);


ALTER TABLE public.vales_combustible OWNER TO postgres;

--
-- Name: vales_combustible_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vales_combustible_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vales_combustible_id_seq OWNER TO postgres;

--
-- Name: vales_combustible_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vales_combustible_id_seq OWNED BY public.vales_combustible.id;


--
-- Name: vehiculos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vehiculos (
    id bigint NOT NULL,
    codigo character varying(50) NOT NULL,
    placa character varying(20) NOT NULL,
    tipo character varying(100),
    industria character varying(100),
    marca character varying(100),
    color character varying(50),
    unidad_id bigint NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    apertura_programatica_id bigint
);


ALTER TABLE public.vehiculos OWNER TO postgres;

--
-- Name: vehiculos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vehiculos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehiculos_id_seq OWNER TO postgres;

--
-- Name: vehiculos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vehiculos_id_seq OWNED BY public.vehiculos.id;


--
-- Name: aperturas_programaticas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aperturas_programaticas ALTER COLUMN id SET DEFAULT nextval('public.aperturas_programaticas_id_seq'::regclass);


--
-- Name: auditorias id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditorias ALTER COLUMN id SET DEFAULT nextval('public.auditorias_id_seq'::regclass);


--
-- Name: entregas_policia id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entregas_policia ALTER COLUMN id SET DEFAULT nextval('public.entregas_policia_id_seq'::regclass);


--
-- Name: estaciones_servicio id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estaciones_servicio ALTER COLUMN id SET DEFAULT nextval('public.estaciones_servicio_id_seq'::regclass);


--
-- Name: fuentes_financiamiento id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fuentes_financiamiento ALTER COLUMN id SET DEFAULT nextval('public.fuentes_financiamiento_id_seq'::regclass);


--
-- Name: lotes_vales id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotes_vales ALTER COLUMN id SET DEFAULT nextval('public.lotes_vales_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: organismos_financiadores id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.organismos_financiadores ALTER COLUMN id SET DEFAULT nextval('public.organismos_financiadores_id_seq'::regclass);


--
-- Name: partes_diarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partes_diarios ALTER COLUMN id SET DEFAULT nextval('public.partes_diarios_id_seq'::regclass);


--
-- Name: periodos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.periodos ALTER COLUMN id SET DEFAULT nextval('public.periodos_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: series_vales id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.series_vales ALTER COLUMN id SET DEFAULT nextval('public.series_vales_id_seq'::regclass);


--
-- Name: solicitudes_contrasena id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.solicitudes_contrasena ALTER COLUMN id SET DEFAULT nextval('public.solicitudes_contrasena_id_seq'::regclass);


--
-- Name: tipos_combustible id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipos_combustible ALTER COLUMN id SET DEFAULT nextval('public.tipos_combustible_id_seq'::regclass);


--
-- Name: unidades id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unidades ALTER COLUMN id SET DEFAULT nextval('public.unidades_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Name: vales_combustible id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible ALTER COLUMN id SET DEFAULT nextval('public.vales_combustible_id_seq'::regclass);


--
-- Name: vehiculos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos ALTER COLUMN id SET DEFAULT nextval('public.vehiculos_id_seq'::regclass);


--
-- Data for Name: aperturas_programaticas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.aperturas_programaticas (id, codigo, descripcion, fuente_financiamiento_id, organismo_financiador_id, estado, created_at, updated_at) FROM stdin;
1	000 0 001	\N	1	3	t	2026-10-22 12:20:11	2026-10-22 12:20:11
2	000 0 001	\N	2	1	t	2026-10-22 12:20:11	2026-10-22 12:20:11
3	130 0 010	\N	1	3	t	2026-10-22 12:20:11	2026-10-22 12:20:11
4	131 0 010	\N	1	3	t	2026-10-22 12:20:11	2026-10-22 12:20:11
5	160 0 024	\N	1	3	t	2026-10-22 12:20:11	2026-10-22 12:20:11
6	210 0 001	\N	2	1	t	2026-10-22 12:20:11	2026-10-22 12:20:11
7	270 0 004	\N	2	1	t	2026-10-22 12:20:11	2026-10-22 12:20:11
8	310 0 012	\N	2	1	t	2026-10-22 12:20:11	2026-10-22 12:20:11
9	331 0 016	\N	2	1	t	2026-10-22 12:20:11	2026-10-22 12:20:11
10	331 0 016	\N	2	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11
\.


--
-- Data for Name: auditorias; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.auditorias (id, usuario_id, accion, tabla, registro_id, datos_anteriores, datos_nuevos, ip, descripcion, created_at, updated_at) FROM stdin;
1	3	ACTUALIZAR	usuarios	3	{"remember_token":"[PROTEGIDO]"}	{"remember_token":"[PROTEGIDO]"}	127.0.0.1	Registro actualizado	2026-10-22 10:31:13	2026-10-22 10:31:13
2	\N	CREAR	fuentes_financiamiento	1	\N	{"codigo":"20","nombre":"FF 20","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":1}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
3	\N	CREAR	fuentes_financiamiento	2	\N	{"codigo":"41","nombre":"FF 41","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":2}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
4	\N	CREAR	organismos_financiadores	1	\N	{"codigo":"113","nombre":"OF 113","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":1}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
5	\N	CREAR	organismos_financiadores	2	\N	{"codigo":"119","nombre":"OF 119","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":2}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
6	\N	CREAR	organismos_financiadores	3	\N	{"codigo":"210","nombre":"OF 210","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":3}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
7	\N	CREAR	unidades	1	\N	{"nombre":"DIRECCION DE MEDIO AMBIENTE Y MADRE TIERRA","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":1}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
8	\N	CREAR	unidades	2	\N	{"nombre":"FUNCIONAMIENTO ALCALDIA MUNICIPAL","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":2}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
9	\N	CREAR	unidades	3	\N	{"nombre":"SERVICIOS Y EQUIPAMIENTO DE EDUCACI\\u00d3N","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":3}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
10	\N	CREAR	unidades	4	\N	{"nombre":"UNIDAD DE GESTION DE RIESGOS","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":4}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
11	\N	CREAR	unidades	5	\N	{"nombre":"UNIDAD DE PARQUES, JARDINES Y SERVICIOS FORESTALES","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":5}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
12	\N	CREAR	unidades	6	\N	{"nombre":"UNIDAD DE SEGURIDAD CIUDADANA","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":6}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
13	\N	CREAR	unidades	7	\N	{"nombre":"UNIDAD DE SERVICIOS ELECTRICOS Y SEMAFORIZACION","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":7}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
14	\N	CREAR	unidades	8	\N	{"nombre":"UNIDAD DE TRANSPORTES Y MAQUINARIAS","estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":8}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
15	\N	CREAR	aperturas_programaticas	1	\N	{"codigo":"000 0 001","fuente_financiamiento_id":1,"organismo_financiador_id":3,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":1}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
16	\N	CREAR	aperturas_programaticas	2	\N	{"codigo":"000 0 001","fuente_financiamiento_id":2,"organismo_financiador_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":2}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
17	\N	CREAR	aperturas_programaticas	3	\N	{"codigo":"130 0 010","fuente_financiamiento_id":1,"organismo_financiador_id":3,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":3}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
18	\N	CREAR	aperturas_programaticas	4	\N	{"codigo":"131 0 010","fuente_financiamiento_id":1,"organismo_financiador_id":3,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":4}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
19	\N	CREAR	aperturas_programaticas	5	\N	{"codigo":"160 0 024","fuente_financiamiento_id":1,"organismo_financiador_id":3,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":5}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
20	\N	CREAR	aperturas_programaticas	6	\N	{"codigo":"210 0 001","fuente_financiamiento_id":2,"organismo_financiador_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":6}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
21	\N	CREAR	aperturas_programaticas	7	\N	{"codigo":"270 0 004","fuente_financiamiento_id":2,"organismo_financiador_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":7}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
22	\N	CREAR	aperturas_programaticas	8	\N	{"codigo":"310 0 012","fuente_financiamiento_id":2,"organismo_financiador_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":8}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
23	\N	CREAR	aperturas_programaticas	9	\N	{"codigo":"331 0 016","fuente_financiamiento_id":2,"organismo_financiador_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":9}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
24	\N	CREAR	aperturas_programaticas	10	\N	{"codigo":"331 0 016","fuente_financiamiento_id":2,"organismo_financiador_id":2,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":10}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
25	\N	CREAR	vehiculos	1	\N	{"codigo":"B-01","placa":"2731NXF","tipo":"BUS COASTER","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":1}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
26	\N	CREAR	vehiculos	2	\N	{"codigo":"C-01","placa":"942GLY","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":2}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
27	\N	CREAR	vehiculos	3	\N	{"codigo":"C-04","placa":"1794HIL","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":3}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
28	\N	CREAR	vehiculos	4	\N	{"codigo":"C-05","placa":"1882FIH","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":4}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
29	\N	CREAR	vehiculos	5	\N	{"codigo":"C-06","placa":"2264FHC","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":5}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
30	\N	CREAR	vehiculos	6	\N	{"codigo":"C-07","placa":"2263XUT","tipo":"CAMIONETA","unidad_id":7,"apertura_programatica_id":5,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":6}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
31	\N	CREAR	vehiculos	7	\N	{"codigo":"C-08","placa":"2295SUT","tipo":"CAMIONETA","unidad_id":8,"apertura_programatica_id":7,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":7}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
32	\N	CREAR	vehiculos	8	\N	{"codigo":"C-09","placa":"2295YTU","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":8}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
33	\N	CREAR	vehiculos	9	\N	{"codigo":"C-10","placa":"2295YUA","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":9}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
34	\N	CREAR	vehiculos	10	\N	{"codigo":"C-11","placa":"2838DLK","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":10}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
35	\N	CREAR	vehiculos	11	\N	{"codigo":"C-12","placa":"2522LIH","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":11}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
36	\N	CREAR	vehiculos	12	\N	{"codigo":"C-13","placa":"2522LKL","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":12}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
37	\N	CREAR	vehiculos	13	\N	{"codigo":"C-15","placa":"2859BGB","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":13}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
38	\N	CREAR	vehiculos	14	\N	{"codigo":"C-16","placa":"2877BLX","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":14}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
39	\N	CREAR	vehiculos	15	\N	{"codigo":"C-17","placa":"2909YTD","tipo":"CAMIONETA","unidad_id":8,"apertura_programatica_id":7,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":15}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
40	\N	CREAR	vehiculos	16	\N	{"codigo":"C-18","placa":"2909YUG","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":16}	127.0.0.1	Registro creado	2026-10-22 12:20:11	2026-10-22 12:20:11
41	\N	CREAR	vehiculos	17	\N	{"codigo":"C-19","placa":"2987CSE","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:11","created_at":"2026-10-22 12:20:11","id":17}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
42	\N	CREAR	vehiculos	18	\N	{"codigo":"C-20","placa":"3012ATS","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":18}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
43	\N	CREAR	vehiculos	19	\N	{"codigo":"C-21","placa":"3028HND","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":19}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
44	\N	CREAR	vehiculos	20	\N	{"codigo":"C-22","placa":"3028HPG","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":20}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
45	\N	CREAR	vehiculos	21	\N	{"codigo":"C-23","placa":"3109XLC","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":21}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
46	\N	CREAR	vehiculos	22	\N	{"codigo":"C-24","placa":"3115TTT","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":22}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
47	\N	CREAR	vehiculos	23	\N	{"codigo":"C-25","placa":"3784ULY","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":23}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
48	\N	CREAR	vehiculos	24	\N	{"codigo":"C-26","placa":"3807XLK","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":24}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
49	\N	CREAR	vehiculos	25	\N	{"codigo":"C-27","placa":"3807XID","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":25}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
50	\N	CREAR	vehiculos	26	\N	{"codigo":"C-28","placa":"3847KAH","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":26}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
51	\N	CREAR	vehiculos	27	\N	{"codigo":"C-29","placa":"3992NII","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":27}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
52	\N	CREAR	vehiculos	28	\N	{"codigo":"C-30","placa":"4016PNF","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":28}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
53	\N	CREAR	vehiculos	29	\N	{"codigo":"C-32","placa":"4034GND","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":29}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
54	\N	CREAR	vehiculos	30	\N	{"codigo":"C-37","placa":"4421FYF","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":30}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
55	\N	CREAR	vehiculos	31	\N	{"codigo":"C-38","placa":"4533DHT","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":31}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
56	\N	CREAR	vehiculos	32	\N	{"codigo":"C-40","placa":"5642XRT","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":32}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
57	\N	CREAR	vehiculos	33	\N	{"codigo":"C-42","placa":"5781DLF","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":33}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
58	\N	CREAR	vehiculos	34	\N	{"codigo":"C-44","placa":"6283KTH","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":34}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
59	\N	CREAR	vehiculos	35	\N	{"codigo":"C-45","placa":"6278GRF","tipo":"CAMIONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":35}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
60	\N	CREAR	vehiculos	36	\N	{"codigo":"CAR-01","placa":"1188CLP","tipo":"FURGONETA (CARRY)","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":36}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
61	\N	CREAR	vehiculos	37	\N	{"codigo":"CAR-02","placa":"1188FCL","tipo":"FURGONETA (CARRY)","unidad_id":4,"apertura_programatica_id":8,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":37}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
62	\N	CREAR	vehiculos	38	\N	{"codigo":"CAR-03","placa":"2568ZZR","tipo":"FURGONETA (CARRY)","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":38}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
63	\N	CREAR	vehiculos	39	\N	{"codigo":"M-08","placa":"1961FXP","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":39}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
64	\N	CREAR	vehiculos	40	\N	{"codigo":"M-16","placa":"1511CRU","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":40}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
65	\N	CREAR	vehiculos	41	\N	{"codigo":"M-17","placa":"1961FHB","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":41}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
66	\N	CREAR	vehiculos	42	\N	{"codigo":"M-19","placa":"1961GBD","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":42}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
67	\N	CREAR	vehiculos	43	\N	{"codigo":"M-21","placa":"1961FDI","tipo":"MOTOCICLETA","unidad_id":3,"apertura_programatica_id":6,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":43}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
68	\N	CREAR	vehiculos	44	\N	{"codigo":"M-22","placa":"1961FAY","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":44}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
69	\N	CREAR	vehiculos	45	\N	{"codigo":"M-23","placa":"1961FCF","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":45}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
70	\N	CREAR	vehiculos	46	\N	{"codigo":"M-24","placa":"1961GTH","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":46}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
71	\N	CREAR	vehiculos	47	\N	{"codigo":"M-27","placa":"2941LKS","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":47}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
72	\N	CREAR	vehiculos	48	\N	{"codigo":"M-33","placa":"2941LDT","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":48}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
73	\N	CREAR	vehiculos	49	\N	{"codigo":"M-34","placa":"2776ADE","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":49}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
74	\N	CREAR	vehiculos	50	\N	{"codigo":"M-35","placa":"2648NNE","tipo":"MOTOCICLETA","unidad_id":4,"apertura_programatica_id":8,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":50}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
75	\N	CREAR	vehiculos	51	\N	{"codigo":"M-36","placa":"2648NPH","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":51}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
76	\N	CREAR	vehiculos	52	\N	{"codigo":"M-46","placa":"3572BHD","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":52}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
77	\N	CREAR	vehiculos	53	\N	{"codigo":"M-49","placa":"3668ALK","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":53}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
78	\N	CREAR	vehiculos	54	\N	{"codigo":"M-50","placa":"3668AKG","tipo":"MOTOCICLETA","unidad_id":1,"apertura_programatica_id":3,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":54}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
79	\N	CREAR	vehiculos	55	\N	{"codigo":"M-51","placa":"3668AGU","tipo":"MOTOCICLETA","unidad_id":8,"apertura_programatica_id":7,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":55}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
80	\N	CREAR	vehiculos	56	\N	{"codigo":"M-52","placa":"3668AHA","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":56}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
81	\N	CREAR	vehiculos	57	\N	{"codigo":"M-53","placa":"3668ADH","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":57}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
82	\N	CREAR	vehiculos	58	\N	{"codigo":"M-54","placa":"3668ABB","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":58}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
83	\N	CREAR	vehiculos	59	\N	{"codigo":"M-55","placa":"3668ACE","tipo":"MOTOCICLETA","unidad_id":4,"apertura_programatica_id":8,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":59}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
84	\N	CREAR	vehiculos	60	\N	{"codigo":"M-59","placa":"3668AID","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":60}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
85	\N	CREAR	vehiculos	61	\N	{"codigo":"M-61","placa":"3689DHK","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":61}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
86	\N	CREAR	vehiculos	62	\N	{"codigo":"M-62","placa":"3689END","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":62}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
87	\N	CREAR	vehiculos	63	\N	{"codigo":"M-69","placa":"3896ZID","tipo":"MOTOCICLETA","unidad_id":5,"apertura_programatica_id":4,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":63}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
88	\N	CREAR	vehiculos	64	\N	{"codigo":"M-71","placa":"4670RPB","tipo":"MOTOCICLETA","unidad_id":7,"apertura_programatica_id":5,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":64}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
89	\N	CREAR	vehiculos	65	\N	{"codigo":"M-72","placa":"5540ARA","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":65}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
90	\N	CREAR	vehiculos	66	\N	{"codigo":"M-73","placa":"5789ESX","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":66}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
91	\N	CREAR	vehiculos	67	\N	{"codigo":"M-74","placa":"5790FFR","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":67}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
92	\N	CREAR	vehiculos	68	\N	{"codigo":"M-78","placa":"6132GDI","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":68}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
93	\N	CREAR	vehiculos	69	\N	{"codigo":"M-80","placa":"6125ZPD","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":69}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
94	\N	CREAR	vehiculos	70	\N	{"codigo":"M-81","placa":"6125ZRG","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":70}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
95	\N	CREAR	vehiculos	71	\N	{"codigo":"M-85","placa":"6125XDP","tipo":"MOTOCICLETA","unidad_id":7,"apertura_programatica_id":5,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":71}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
96	\N	CREAR	vehiculos	72	\N	{"codigo":"M-89","placa":"6569FED","tipo":"MOTOCICLETA","unidad_id":7,"apertura_programatica_id":5,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":72}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
97	\N	CREAR	vehiculos	73	\N	{"codigo":"M-90","placa":"6569FFG","tipo":"MOTOCICLETA","unidad_id":3,"apertura_programatica_id":6,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":73}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
98	\N	CREAR	vehiculos	74	\N	{"codigo":"M-91","placa":"6605EKS","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":74}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
99	\N	CREAR	vehiculos	75	\N	{"codigo":"M-92","placa":"6606PYY","tipo":"MOTOCICLETA","unidad_id":8,"apertura_programatica_id":7,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":75}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
100	\N	CREAR	vehiculos	76	\N	{"codigo":"M-93","placa":"6773YIB","tipo":"MOTOCICLETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":76}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
101	\N	CREAR	vehiculos	77	\N	{"codigo":"V-04","placa":"1193AST","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":77}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
102	\N	CREAR	vehiculos	78	\N	{"codigo":"V-09","placa":"3445UGG","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":78}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
103	\N	CREAR	vehiculos	79	\N	{"codigo":"V-13","placa":"5744TSS","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":79}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
104	\N	CREAR	vehiculos	80	\N	{"codigo":"V-14","placa":"3876YNP","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":80}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
105	\N	CREAR	vehiculos	81	\N	{"codigo":"VD-09","placa":"2109KYX","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":81}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
106	\N	CREAR	vehiculos	82	\N	{"codigo":"VD-10","placa":"2211RNR","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":82}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
107	\N	CREAR	vehiculos	83	\N	{"codigo":"VD-12","placa":"5325DNI","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":83}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
108	\N	CREAR	vehiculos	84	\N	{"codigo":"VD-14","placa":"2511CKF","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":84}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
109	\N	CREAR	vehiculos	85	\N	{"codigo":"VD-15","placa":"4795GYN","tipo":"VAGONETA","unidad_id":8,"apertura_programatica_id":7,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":85}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
110	\N	CREAR	vehiculos	86	\N	{"codigo":"VD-16","placa":"4124SIT","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":1,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":86}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
111	\N	CREAR	vehiculos	87	\N	{"codigo":"VD-18","placa":"4103CTY","tipo":"VAGONETA","unidad_id":5,"apertura_programatica_id":4,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":87}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
112	\N	CREAR	vehiculos	88	\N	{"codigo":"VD-22","placa":"4556RBI","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":88}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
113	\N	CREAR	vehiculos	89	\N	{"codigo":"VD-23","placa":"1708FTS","tipo":"VAGONETA","unidad_id":8,"apertura_programatica_id":7,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":89}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
114	\N	CREAR	vehiculos	90	\N	{"codigo":"VD-25","placa":"2534TCX","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":90}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
115	\N	CREAR	vehiculos	91	\N	{"codigo":"VD-27","placa":"4148CHI","tipo":"VAGONETA","unidad_id":2,"apertura_programatica_id":2,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":91}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
116	\N	CREAR	vehiculos	92	\N	{"codigo":"AP-01","placa":"2120CAG","tipo":"AMBULACIA POLICIAL","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":92}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
117	\N	CREAR	vehiculos	93	\N	{"codigo":"AP-02","placa":"5747EIG","tipo":"AMBULACIA POLICIAL","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":93}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
118	\N	CREAR	vehiculos	94	\N	{"codigo":"CP-02","placa":"2101TXC","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":94}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
119	\N	CREAR	vehiculos	95	\N	{"codigo":"CP-03","placa":"2101TYF","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":95}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
120	\N	CREAR	vehiculos	96	\N	{"codigo":"CP-04","placa":"2263ULT","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":96}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
121	\N	CREAR	vehiculos	97	\N	{"codigo":"CP-06","placa":"2893DSC","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":97}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
122	\N	CREAR	vehiculos	98	\N	{"codigo":"CP-07","placa":"2893DTF","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":98}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
123	\N	CREAR	vehiculos	99	\N	{"codigo":"CP-08","placa":"2981BPP","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":99}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
124	\N	CREAR	vehiculos	100	\N	{"codigo":"CP-09","placa":"3063KXF","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":100}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
125	\N	CREAR	vehiculos	101	\N	{"codigo":"CP-10","placa":"3063KUC","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":101}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
126	\N	CREAR	vehiculos	102	\N	{"codigo":"CP-11","placa":"3063KYI","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":9,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":102}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
127	\N	CREAR	vehiculos	103	\N	{"codigo":"CP-13","placa":"3447DRK","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":103}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
128	\N	CREAR	vehiculos	104	\N	{"codigo":"CP-14","placa":"3447DPG","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":104}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
129	\N	CREAR	vehiculos	105	\N	{"codigo":"CP-16","placa":"3623RXL","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":105}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
130	\N	CREAR	vehiculos	106	\N	{"codigo":"CP-17","placa":"3623RYR","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":106}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
131	\N	CREAR	vehiculos	107	\N	{"codigo":"CP-20","placa":"4810CXS","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":107}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
132	\N	CREAR	vehiculos	108	\N	{"codigo":"CP-21","placa":"5185XPU","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":108}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
133	\N	CREAR	vehiculos	109	\N	{"codigo":"CP-22","placa":"5202TBF","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":109}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
134	\N	CREAR	vehiculos	110	\N	{"codigo":"CP-23","placa":"6278GSI","tipo":"CAMIONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":110}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
135	\N	CREAR	vehiculos	111	\N	{"codigo":"CP-24","placa":"6484PCD","tipo":"VAGONETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":111}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
136	\N	CREAR	vehiculos	112	\N	{"codigo":"MP-01","placa":"1961FGX","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":112}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
137	\N	CREAR	vehiculos	113	\N	{"codigo":"MP-21","placa":"3171EXX","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":113}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
138	\N	CREAR	vehiculos	114	\N	{"codigo":"MP-26","placa":"3171GSL","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":114}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
139	\N	CREAR	vehiculos	115	\N	{"codigo":"MP-27","placa":"3171GRH","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":115}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
140	\N	CREAR	vehiculos	116	\N	{"codigo":"MP-34","placa":"3310FEA","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":116}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
141	\N	CREAR	vehiculos	117	\N	{"codigo":"MP-37","placa":"3310FBL","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":117}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
142	\N	CREAR	vehiculos	118	\N	{"codigo":"MP-38","placa":"3310FAH","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":118}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
143	\N	CREAR	vehiculos	119	\N	{"codigo":"MP-41","placa":"3533THH","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":119}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
144	\N	CREAR	vehiculos	120	\N	{"codigo":"MP-50","placa":"3533TGE","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":120}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
145	\N	CREAR	vehiculos	121	\N	{"codigo":"MP-51","placa":"3533TCN","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":121}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
146	\N	CREAR	vehiculos	122	\N	{"codigo":"MP-52","placa":"3533TAF","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":122}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
147	\N	CREAR	vehiculos	123	\N	{"codigo":"MP-54","placa":"4885RNL","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":123}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
148	\N	CREAR	vehiculos	124	\N	{"codigo":"MP-55","placa":"4891ECP","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":124}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
149	\N	CREAR	vehiculos	125	\N	{"codigo":"MP-56","placa":"4891EDT","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":125}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
150	\N	CREAR	vehiculos	126	\N	{"codigo":"MP-57","placa":"4962DAH","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":126}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
151	\N	CREAR	vehiculos	127	\N	{"codigo":"MP-58","placa":"4962DBL","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":127}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
152	\N	CREAR	vehiculos	128	\N	{"codigo":"MP-59","placa":"4962DCR","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":128}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
153	\N	CREAR	vehiculos	129	\N	{"codigo":"MP-60","placa":"5557XZX","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":129}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
154	\N	CREAR	vehiculos	130	\N	{"codigo":"MP-61","placa":"5557YAY","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":130}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
155	\N	CREAR	vehiculos	131	\N	{"codigo":"MP-62","placa":"5557XUI","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":131}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
156	\N	CREAR	vehiculos	132	\N	{"codigo":"MP-63","placa":"5557XXN","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":132}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
157	\N	CREAR	vehiculos	133	\N	{"codigo":"MP-64","placa":"5557XYS","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":133}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
158	\N	CREAR	vehiculos	134	\N	{"codigo":"MP-65","placa":"5557YBC","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":134}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
159	\N	CREAR	vehiculos	135	\N	{"codigo":"MP-66","placa":"5890GDN","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":135}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
160	\N	CREAR	vehiculos	136	\N	{"codigo":"MP-67","placa":"6111CKU","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":136}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
161	\N	CREAR	vehiculos	137	\N	{"codigo":"MP-68","placa":"6111CIR","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":137}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
162	\N	CREAR	vehiculos	138	\N	{"codigo":"MP-69","placa":"6518HHA","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":138}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
163	\N	CREAR	vehiculos	139	\N	{"codigo":"MP-70","placa":"6518HGU","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":139}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
164	\N	CREAR	vehiculos	140	\N	{"codigo":"MP-71","placa":"6518HFR","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":140}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
165	\N	CREAR	vehiculos	141	\N	{"codigo":"MP-72","placa":"6518HEL","tipo":"MOTOCICLETA","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":141}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
166	\N	CREAR	vehiculos	142	\N	{"codigo":"VP-03","placa":"5221YUH","tipo":"VAGONETA POLICIAL","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":142}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
167	\N	CREAR	vehiculos	143	\N	{"codigo":"VP-04","placa":"5677ZRD","tipo":"VAGONETA POLICIAL","unidad_id":6,"apertura_programatica_id":10,"estado":true,"updated_at":"2026-10-22 12:20:12","created_at":"2026-10-22 12:20:12","id":143}	127.0.0.1	Registro creado	2026-10-22 12:20:12	2026-10-22 12:20:12
168	3	CREAR	tipos_combustible	1	\N	{"nombre":"GASOLINA","unidad_medida":"Litros","estado":"1","updated_at":"2026-10-22 12:25:53","created_at":"2026-10-22 12:25:53","id":1}	127.0.0.1	Registro creado	2026-10-22 12:25:53	2026-10-22 12:25:53
169	3	CREAR	tipos_combustible	2	\N	{"nombre":"DIESEL","unidad_medida":"Litros","estado":"1","updated_at":"2026-10-22 12:26:06","created_at":"2026-10-22 12:26:06","id":2}	127.0.0.1	Registro creado	2026-10-22 12:26:06	2026-10-22 12:26:06
170	3	CREAR	estaciones_servicio	1	\N	{"nombre":"CITY TRUCK","direccion":"AV.HUALLYANY","telefono":"12345454","estado":"1","updated_at":"2026-10-22 12:26:47","created_at":"2026-10-22 12:26:47","id":1}	127.0.0.1	Registro creado	2026-10-22 12:26:47	2026-10-22 12:26:47
171	3	CREAR	estaciones_servicio	2	\N	{"nombre":"YPF","direccion":"AV.CHAPARE","telefono":"71476393","estado":"1","updated_at":"2026-10-22 12:27:19","created_at":"2026-10-22 12:27:19","id":2}	127.0.0.1	Registro creado	2026-10-22 12:27:19	2026-10-22 12:27:19
172	3	CREAR	periodos	1	\N	{"mes":1,"anio":"2026","fecha_inicio":"2026-01-01 00:00:00","fecha_fin":"2026-01-31 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":1}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
173	3	CREAR	periodos	2	\N	{"mes":2,"anio":"2026","fecha_inicio":"2026-02-01 00:00:00","fecha_fin":"2026-02-28 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":2}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
174	3	CREAR	periodos	3	\N	{"mes":3,"anio":"2026","fecha_inicio":"2026-03-01 00:00:00","fecha_fin":"2026-03-31 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":3}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
175	3	CREAR	periodos	4	\N	{"mes":4,"anio":"2026","fecha_inicio":"2026-04-01 00:00:00","fecha_fin":"2026-04-30 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":4}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
176	3	CREAR	periodos	5	\N	{"mes":5,"anio":"2026","fecha_inicio":"2026-05-01 00:00:00","fecha_fin":"2026-05-31 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":5}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
177	3	CREAR	periodos	6	\N	{"mes":6,"anio":"2026","fecha_inicio":"2026-06-01 00:00:00","fecha_fin":"2026-06-30 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":6}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
178	3	CREAR	periodos	7	\N	{"mes":7,"anio":"2026","fecha_inicio":"2026-07-01 00:00:00","fecha_fin":"2026-07-31 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":7}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
179	3	CREAR	periodos	8	\N	{"mes":8,"anio":"2026","fecha_inicio":"2026-08-01 00:00:00","fecha_fin":"2026-08-31 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":8}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
180	3	CREAR	periodos	9	\N	{"mes":9,"anio":"2026","fecha_inicio":"2026-09-01 00:00:00","fecha_fin":"2026-09-30 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":9}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
181	3	CREAR	periodos	10	\N	{"mes":10,"anio":"2026","fecha_inicio":"2026-10-01 00:00:00","fecha_fin":"2026-10-31 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":10}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
182	3	CREAR	periodos	11	\N	{"mes":11,"anio":"2026","fecha_inicio":"2026-11-01 00:00:00","fecha_fin":"2026-11-30 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":11}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
183	3	CREAR	periodos	12	\N	{"mes":12,"anio":"2026","fecha_inicio":"2026-12-01 00:00:00","fecha_fin":"2026-12-31 23:59:59","estado":true,"updated_at":"2026-10-22 12:29:36","created_at":"2026-10-22 12:29:36","id":12}	127.0.0.1	Registro creado	2026-10-22 12:29:36	2026-10-22 12:29:36
184	3	CREAR	lotes_vales	1	\N	{"numero_inicial":"2070","numero_final":"2270","fecha_recepcion":"2026-10-22 00:00:00","periodo_id":"10","estado":"1","updated_at":"2026-10-22 12:41:34","created_at":"2026-10-22 12:41:34","id":1}	127.0.0.1	Registro creado	2026-10-22 12:41:35	2026-10-22 12:41:35
185	3	ELIMINAR	lotes_vales	1	{"id":1,"numero_inicial":2070,"numero_final":2270,"fecha_recepcion":"2026-10-22T04:00:00.000000Z","periodo_id":10,"estado":true,"created_at":"2026-10-22T16:41:34.000000Z","updated_at":"2026-10-22T16:41:34.000000Z"}	\N	127.0.0.1	Registro eliminado	2026-10-22 12:41:47	2026-10-22 12:41:47
186	3	CREAR	lotes_vales	2	\N	{"numero_inicial":"2031","numero_final":"2234","fecha_recepcion":"2026-10-22 00:00:00","periodo_id":"7","estado":"1","updated_at":"2026-10-22 12:47:40","created_at":"2026-10-22 12:47:40","id":2}	127.0.0.1	Registro creado	2026-10-22 12:47:40	2026-10-22 12:47:40
187	3	ELIMINAR	lotes_vales	2	{"id":2,"numero_inicial":2031,"numero_final":2234,"fecha_recepcion":"2026-10-22T04:00:00.000000Z","periodo_id":7,"estado":true,"created_at":"2026-10-22T16:47:40.000000Z","updated_at":"2026-10-22T16:47:40.000000Z"}	\N	127.0.0.1	Registro eliminado	2026-10-22 12:47:47	2026-10-22 12:47:47
188	3	CREAR	lotes_vales	3	\N	{"numero_inicial":"2071","numero_final":"2270","fecha_recepcion":"2026-10-22 00:00:00","periodo_id":"10","estado":"1","updated_at":"2026-10-22 12:48:18","created_at":"2026-10-22 12:48:18","id":3}	127.0.0.1	Registro creado	2026-10-22 12:48:18	2026-10-22 12:48:18
1389	3	CREAR	vales_combustible	601	\N	{"numero_vale":2071,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":601}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1390	3	CREAR	vales_combustible	601	\N	{"numero_vale":2071,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":601}	127.0.0.1	Se creó el vale de combustible N.º 2071	2026-10-22 12:56:37	2026-10-22 12:56:37
1391	3	CREAR	vales_combustible	602	\N	{"numero_vale":2072,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":602}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1392	3	CREAR	vales_combustible	602	\N	{"numero_vale":2072,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":602}	127.0.0.1	Se creó el vale de combustible N.º 2072	2026-10-22 12:56:37	2026-10-22 12:56:37
1393	3	CREAR	vales_combustible	603	\N	{"numero_vale":2073,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":603}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1394	3	CREAR	vales_combustible	603	\N	{"numero_vale":2073,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":603}	127.0.0.1	Se creó el vale de combustible N.º 2073	2026-10-22 12:56:37	2026-10-22 12:56:37
1395	3	CREAR	vales_combustible	604	\N	{"numero_vale":2074,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":604}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1396	3	CREAR	vales_combustible	604	\N	{"numero_vale":2074,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":604}	127.0.0.1	Se creó el vale de combustible N.º 2074	2026-10-22 12:56:37	2026-10-22 12:56:37
1397	3	CREAR	vales_combustible	605	\N	{"numero_vale":2075,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":605}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1398	3	CREAR	vales_combustible	605	\N	{"numero_vale":2075,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":605}	127.0.0.1	Se creó el vale de combustible N.º 2075	2026-10-22 12:56:37	2026-10-22 12:56:37
1399	3	CREAR	vales_combustible	606	\N	{"numero_vale":2076,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":606}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1400	3	CREAR	vales_combustible	606	\N	{"numero_vale":2076,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":606}	127.0.0.1	Se creó el vale de combustible N.º 2076	2026-10-22 12:56:37	2026-10-22 12:56:37
1401	3	CREAR	vales_combustible	607	\N	{"numero_vale":2077,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":607}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1402	3	CREAR	vales_combustible	607	\N	{"numero_vale":2077,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":607}	127.0.0.1	Se creó el vale de combustible N.º 2077	2026-10-22 12:56:37	2026-10-22 12:56:37
1403	3	CREAR	vales_combustible	608	\N	{"numero_vale":2078,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":608}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1404	3	CREAR	vales_combustible	608	\N	{"numero_vale":2078,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":608}	127.0.0.1	Se creó el vale de combustible N.º 2078	2026-10-22 12:56:37	2026-10-22 12:56:37
1405	3	CREAR	vales_combustible	609	\N	{"numero_vale":2079,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":609}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1406	3	CREAR	vales_combustible	609	\N	{"numero_vale":2079,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":609}	127.0.0.1	Se creó el vale de combustible N.º 2079	2026-10-22 12:56:37	2026-10-22 12:56:37
1795	3	ACTUALIZAR	vales_combustible	802	{"updated_at":"2026-10-22T17:03:28.000000Z","estacion_servicio_id":null,"tipo_combustible_id":null}	{"updated_at":"2026-10-22 13:42:26","estacion_servicio_id":"1","tipo_combustible_id":"2"}	127.0.0.1	Registro actualizado	2026-10-22 13:42:26	2026-10-22 13:42:26
1407	3	CREAR	vales_combustible	610	\N	{"numero_vale":2080,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":610}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1408	3	CREAR	vales_combustible	610	\N	{"numero_vale":2080,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":610}	127.0.0.1	Se creó el vale de combustible N.º 2080	2026-10-22 12:56:37	2026-10-22 12:56:37
1409	3	CREAR	vales_combustible	611	\N	{"numero_vale":2081,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":611}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1410	3	CREAR	vales_combustible	611	\N	{"numero_vale":2081,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":611}	127.0.0.1	Se creó el vale de combustible N.º 2081	2026-10-22 12:56:37	2026-10-22 12:56:37
1411	3	CREAR	vales_combustible	612	\N	{"numero_vale":2082,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":612}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1412	3	CREAR	vales_combustible	612	\N	{"numero_vale":2082,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":612}	127.0.0.1	Se creó el vale de combustible N.º 2082	2026-10-22 12:56:37	2026-10-22 12:56:37
1413	3	CREAR	vales_combustible	613	\N	{"numero_vale":2083,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":613}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1414	3	CREAR	vales_combustible	613	\N	{"numero_vale":2083,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":613}	127.0.0.1	Se creó el vale de combustible N.º 2083	2026-10-22 12:56:37	2026-10-22 12:56:37
1415	3	CREAR	vales_combustible	614	\N	{"numero_vale":2084,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":614}	127.0.0.1	Registro creado	2026-10-22 12:56:37	2026-10-22 12:56:37
1416	3	CREAR	vales_combustible	614	\N	{"numero_vale":2084,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:37","created_at":"2026-10-22 12:56:37","id":614}	127.0.0.1	Se creó el vale de combustible N.º 2084	2026-10-22 12:56:37	2026-10-22 12:56:37
1417	3	CREAR	vales_combustible	615	\N	{"numero_vale":2085,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":615}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1418	3	CREAR	vales_combustible	615	\N	{"numero_vale":2085,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":615}	127.0.0.1	Se creó el vale de combustible N.º 2085	2026-10-22 12:56:38	2026-10-22 12:56:38
1419	3	CREAR	vales_combustible	616	\N	{"numero_vale":2086,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":616}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1420	3	CREAR	vales_combustible	616	\N	{"numero_vale":2086,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":616}	127.0.0.1	Se creó el vale de combustible N.º 2086	2026-10-22 12:56:38	2026-10-22 12:56:38
1421	3	CREAR	vales_combustible	617	\N	{"numero_vale":2087,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":617}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1422	3	CREAR	vales_combustible	617	\N	{"numero_vale":2087,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":617}	127.0.0.1	Se creó el vale de combustible N.º 2087	2026-10-22 12:56:38	2026-10-22 12:56:38
1423	3	CREAR	vales_combustible	618	\N	{"numero_vale":2088,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":618}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1424	3	CREAR	vales_combustible	618	\N	{"numero_vale":2088,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":618}	127.0.0.1	Se creó el vale de combustible N.º 2088	2026-10-22 12:56:38	2026-10-22 12:56:38
1425	3	CREAR	vales_combustible	619	\N	{"numero_vale":2089,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":619}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1426	3	CREAR	vales_combustible	619	\N	{"numero_vale":2089,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":619}	127.0.0.1	Se creó el vale de combustible N.º 2089	2026-10-22 12:56:38	2026-10-22 12:56:38
1427	3	CREAR	vales_combustible	620	\N	{"numero_vale":2090,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":620}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1428	3	CREAR	vales_combustible	620	\N	{"numero_vale":2090,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":620}	127.0.0.1	Se creó el vale de combustible N.º 2090	2026-10-22 12:56:38	2026-10-22 12:56:38
1429	3	CREAR	vales_combustible	621	\N	{"numero_vale":2091,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":621}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1430	3	CREAR	vales_combustible	621	\N	{"numero_vale":2091,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":621}	127.0.0.1	Se creó el vale de combustible N.º 2091	2026-10-22 12:56:38	2026-10-22 12:56:38
1431	3	CREAR	vales_combustible	622	\N	{"numero_vale":2092,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":622}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1432	3	CREAR	vales_combustible	622	\N	{"numero_vale":2092,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":622}	127.0.0.1	Se creó el vale de combustible N.º 2092	2026-10-22 12:56:38	2026-10-22 12:56:38
1433	3	CREAR	vales_combustible	623	\N	{"numero_vale":2093,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":623}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1434	3	CREAR	vales_combustible	623	\N	{"numero_vale":2093,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":623}	127.0.0.1	Se creó el vale de combustible N.º 2093	2026-10-22 12:56:38	2026-10-22 12:56:38
1435	3	CREAR	vales_combustible	624	\N	{"numero_vale":2094,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":624}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1436	3	CREAR	vales_combustible	624	\N	{"numero_vale":2094,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":624}	127.0.0.1	Se creó el vale de combustible N.º 2094	2026-10-22 12:56:38	2026-10-22 12:56:38
1437	3	CREAR	vales_combustible	625	\N	{"numero_vale":2095,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":625}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1438	3	CREAR	vales_combustible	625	\N	{"numero_vale":2095,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":625}	127.0.0.1	Se creó el vale de combustible N.º 2095	2026-10-22 12:56:38	2026-10-22 12:56:38
1439	3	CREAR	vales_combustible	626	\N	{"numero_vale":2096,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":626}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1440	3	CREAR	vales_combustible	626	\N	{"numero_vale":2096,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":626}	127.0.0.1	Se creó el vale de combustible N.º 2096	2026-10-22 12:56:38	2026-10-22 12:56:38
1441	3	CREAR	vales_combustible	627	\N	{"numero_vale":2097,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":627}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1442	3	CREAR	vales_combustible	627	\N	{"numero_vale":2097,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":627}	127.0.0.1	Se creó el vale de combustible N.º 2097	2026-10-22 12:56:38	2026-10-22 12:56:38
1443	3	CREAR	vales_combustible	628	\N	{"numero_vale":2098,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":628}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1444	3	CREAR	vales_combustible	628	\N	{"numero_vale":2098,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":628}	127.0.0.1	Se creó el vale de combustible N.º 2098	2026-10-22 12:56:38	2026-10-22 12:56:38
1445	3	CREAR	vales_combustible	629	\N	{"numero_vale":2099,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":629}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1446	3	CREAR	vales_combustible	629	\N	{"numero_vale":2099,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":629}	127.0.0.1	Se creó el vale de combustible N.º 2099	2026-10-22 12:56:38	2026-10-22 12:56:38
1447	3	CREAR	vales_combustible	630	\N	{"numero_vale":2100,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":630}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1448	3	CREAR	vales_combustible	630	\N	{"numero_vale":2100,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":630}	127.0.0.1	Se creó el vale de combustible N.º 2100	2026-10-22 12:56:38	2026-10-22 12:56:38
1449	3	CREAR	vales_combustible	631	\N	{"numero_vale":2101,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":631}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1450	3	CREAR	vales_combustible	631	\N	{"numero_vale":2101,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":631}	127.0.0.1	Se creó el vale de combustible N.º 2101	2026-10-22 12:56:38	2026-10-22 12:56:38
1451	3	CREAR	vales_combustible	632	\N	{"numero_vale":2102,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":632}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1452	3	CREAR	vales_combustible	632	\N	{"numero_vale":2102,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":632}	127.0.0.1	Se creó el vale de combustible N.º 2102	2026-10-22 12:56:38	2026-10-22 12:56:38
1453	3	CREAR	vales_combustible	633	\N	{"numero_vale":2103,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":633}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1454	3	CREAR	vales_combustible	633	\N	{"numero_vale":2103,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":633}	127.0.0.1	Se creó el vale de combustible N.º 2103	2026-10-22 12:56:38	2026-10-22 12:56:38
1455	3	CREAR	vales_combustible	634	\N	{"numero_vale":2104,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":634}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1456	3	CREAR	vales_combustible	634	\N	{"numero_vale":2104,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":634}	127.0.0.1	Se creó el vale de combustible N.º 2104	2026-10-22 12:56:38	2026-10-22 12:56:38
1457	3	CREAR	vales_combustible	635	\N	{"numero_vale":2105,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":635}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1458	3	CREAR	vales_combustible	635	\N	{"numero_vale":2105,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":635}	127.0.0.1	Se creó el vale de combustible N.º 2105	2026-10-22 12:56:38	2026-10-22 12:56:38
1459	3	CREAR	vales_combustible	636	\N	{"numero_vale":2106,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":636}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1460	3	CREAR	vales_combustible	636	\N	{"numero_vale":2106,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":636}	127.0.0.1	Se creó el vale de combustible N.º 2106	2026-10-22 12:56:38	2026-10-22 12:56:38
1461	3	CREAR	vales_combustible	637	\N	{"numero_vale":2107,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":637}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1462	3	CREAR	vales_combustible	637	\N	{"numero_vale":2107,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":637}	127.0.0.1	Se creó el vale de combustible N.º 2107	2026-10-22 12:56:38	2026-10-22 12:56:38
1463	3	CREAR	vales_combustible	638	\N	{"numero_vale":2108,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":638}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1464	3	CREAR	vales_combustible	638	\N	{"numero_vale":2108,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":638}	127.0.0.1	Se creó el vale de combustible N.º 2108	2026-10-22 12:56:38	2026-10-22 12:56:38
1465	3	CREAR	vales_combustible	639	\N	{"numero_vale":2109,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":639}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1466	3	CREAR	vales_combustible	639	\N	{"numero_vale":2109,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":639}	127.0.0.1	Se creó el vale de combustible N.º 2109	2026-10-22 12:56:38	2026-10-22 12:56:38
1467	3	CREAR	vales_combustible	640	\N	{"numero_vale":2110,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":640}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1468	3	CREAR	vales_combustible	640	\N	{"numero_vale":2110,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":640}	127.0.0.1	Se creó el vale de combustible N.º 2110	2026-10-22 12:56:38	2026-10-22 12:56:38
1469	3	CREAR	vales_combustible	641	\N	{"numero_vale":2111,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":641}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1470	3	CREAR	vales_combustible	641	\N	{"numero_vale":2111,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":641}	127.0.0.1	Se creó el vale de combustible N.º 2111	2026-10-22 12:56:38	2026-10-22 12:56:38
1471	3	CREAR	vales_combustible	642	\N	{"numero_vale":2112,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":642}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1472	3	CREAR	vales_combustible	642	\N	{"numero_vale":2112,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":642}	127.0.0.1	Se creó el vale de combustible N.º 2112	2026-10-22 12:56:38	2026-10-22 12:56:38
1473	3	CREAR	vales_combustible	643	\N	{"numero_vale":2113,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":643}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1474	3	CREAR	vales_combustible	643	\N	{"numero_vale":2113,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":643}	127.0.0.1	Se creó el vale de combustible N.º 2113	2026-10-22 12:56:38	2026-10-22 12:56:38
1475	3	CREAR	vales_combustible	644	\N	{"numero_vale":2114,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":644}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1476	3	CREAR	vales_combustible	644	\N	{"numero_vale":2114,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":644}	127.0.0.1	Se creó el vale de combustible N.º 2114	2026-10-22 12:56:38	2026-10-22 12:56:38
1477	3	CREAR	vales_combustible	645	\N	{"numero_vale":2115,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":645}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1478	3	CREAR	vales_combustible	645	\N	{"numero_vale":2115,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":645}	127.0.0.1	Se creó el vale de combustible N.º 2115	2026-10-22 12:56:38	2026-10-22 12:56:38
1479	3	CREAR	vales_combustible	646	\N	{"numero_vale":2116,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":646}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1480	3	CREAR	vales_combustible	646	\N	{"numero_vale":2116,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":646}	127.0.0.1	Se creó el vale de combustible N.º 2116	2026-10-22 12:56:38	2026-10-22 12:56:38
1481	3	CREAR	vales_combustible	647	\N	{"numero_vale":2117,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":647}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1482	3	CREAR	vales_combustible	647	\N	{"numero_vale":2117,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":647}	127.0.0.1	Se creó el vale de combustible N.º 2117	2026-10-22 12:56:38	2026-10-22 12:56:38
1483	3	CREAR	vales_combustible	648	\N	{"numero_vale":2118,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":648}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1484	3	CREAR	vales_combustible	648	\N	{"numero_vale":2118,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":648}	127.0.0.1	Se creó el vale de combustible N.º 2118	2026-10-22 12:56:38	2026-10-22 12:56:38
1485	3	CREAR	vales_combustible	649	\N	{"numero_vale":2119,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":649}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1486	3	CREAR	vales_combustible	649	\N	{"numero_vale":2119,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":649}	127.0.0.1	Se creó el vale de combustible N.º 2119	2026-10-22 12:56:38	2026-10-22 12:56:38
1487	3	CREAR	vales_combustible	650	\N	{"numero_vale":2120,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":650}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1488	3	CREAR	vales_combustible	650	\N	{"numero_vale":2120,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":650}	127.0.0.1	Se creó el vale de combustible N.º 2120	2026-10-22 12:56:38	2026-10-22 12:56:38
1489	3	CREAR	vales_combustible	651	\N	{"numero_vale":2121,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":651}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1490	3	CREAR	vales_combustible	651	\N	{"numero_vale":2121,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":651}	127.0.0.1	Se creó el vale de combustible N.º 2121	2026-10-22 12:56:38	2026-10-22 12:56:38
1491	3	CREAR	vales_combustible	652	\N	{"numero_vale":2122,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":652}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1492	3	CREAR	vales_combustible	652	\N	{"numero_vale":2122,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":652}	127.0.0.1	Se creó el vale de combustible N.º 2122	2026-10-22 12:56:38	2026-10-22 12:56:38
1493	3	CREAR	vales_combustible	653	\N	{"numero_vale":2123,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":653}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1494	3	CREAR	vales_combustible	653	\N	{"numero_vale":2123,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":653}	127.0.0.1	Se creó el vale de combustible N.º 2123	2026-10-22 12:56:38	2026-10-22 12:56:38
1495	3	CREAR	vales_combustible	654	\N	{"numero_vale":2124,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":654}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1496	3	CREAR	vales_combustible	654	\N	{"numero_vale":2124,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":654}	127.0.0.1	Se creó el vale de combustible N.º 2124	2026-10-22 12:56:38	2026-10-22 12:56:38
1497	3	CREAR	vales_combustible	655	\N	{"numero_vale":2125,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":655}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1498	3	CREAR	vales_combustible	655	\N	{"numero_vale":2125,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":655}	127.0.0.1	Se creó el vale de combustible N.º 2125	2026-10-22 12:56:38	2026-10-22 12:56:38
1499	3	CREAR	vales_combustible	656	\N	{"numero_vale":2126,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":656}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1500	3	CREAR	vales_combustible	656	\N	{"numero_vale":2126,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":656}	127.0.0.1	Se creó el vale de combustible N.º 2126	2026-10-22 12:56:38	2026-10-22 12:56:38
1501	3	CREAR	vales_combustible	657	\N	{"numero_vale":2127,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":657}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1502	3	CREAR	vales_combustible	657	\N	{"numero_vale":2127,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":657}	127.0.0.1	Se creó el vale de combustible N.º 2127	2026-10-22 12:56:38	2026-10-22 12:56:38
1503	3	CREAR	vales_combustible	658	\N	{"numero_vale":2128,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":658}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1504	3	CREAR	vales_combustible	658	\N	{"numero_vale":2128,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":658}	127.0.0.1	Se creó el vale de combustible N.º 2128	2026-10-22 12:56:38	2026-10-22 12:56:38
1505	3	CREAR	vales_combustible	659	\N	{"numero_vale":2129,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":659}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1506	3	CREAR	vales_combustible	659	\N	{"numero_vale":2129,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":659}	127.0.0.1	Se creó el vale de combustible N.º 2129	2026-10-22 12:56:38	2026-10-22 12:56:38
1507	3	CREAR	vales_combustible	660	\N	{"numero_vale":2130,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":660}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1508	3	CREAR	vales_combustible	660	\N	{"numero_vale":2130,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":660}	127.0.0.1	Se creó el vale de combustible N.º 2130	2026-10-22 12:56:38	2026-10-22 12:56:38
1509	3	CREAR	vales_combustible	661	\N	{"numero_vale":2131,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":661}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1510	3	CREAR	vales_combustible	661	\N	{"numero_vale":2131,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":661}	127.0.0.1	Se creó el vale de combustible N.º 2131	2026-10-22 12:56:38	2026-10-22 12:56:38
1511	3	CREAR	vales_combustible	662	\N	{"numero_vale":2132,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":662}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1512	3	CREAR	vales_combustible	662	\N	{"numero_vale":2132,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":662}	127.0.0.1	Se creó el vale de combustible N.º 2132	2026-10-22 12:56:38	2026-10-22 12:56:38
1513	3	CREAR	vales_combustible	663	\N	{"numero_vale":2133,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":663}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1514	3	CREAR	vales_combustible	663	\N	{"numero_vale":2133,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":663}	127.0.0.1	Se creó el vale de combustible N.º 2133	2026-10-22 12:56:38	2026-10-22 12:56:38
1515	3	CREAR	vales_combustible	664	\N	{"numero_vale":2134,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":664}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1516	3	CREAR	vales_combustible	664	\N	{"numero_vale":2134,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":664}	127.0.0.1	Se creó el vale de combustible N.º 2134	2026-10-22 12:56:38	2026-10-22 12:56:38
1517	3	CREAR	vales_combustible	665	\N	{"numero_vale":2135,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":665}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1518	3	CREAR	vales_combustible	665	\N	{"numero_vale":2135,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":665}	127.0.0.1	Se creó el vale de combustible N.º 2135	2026-10-22 12:56:38	2026-10-22 12:56:38
1519	3	CREAR	vales_combustible	666	\N	{"numero_vale":2136,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":666}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1520	3	CREAR	vales_combustible	666	\N	{"numero_vale":2136,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":666}	127.0.0.1	Se creó el vale de combustible N.º 2136	2026-10-22 12:56:38	2026-10-22 12:56:38
1521	3	CREAR	vales_combustible	667	\N	{"numero_vale":2137,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":667}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1522	3	CREAR	vales_combustible	667	\N	{"numero_vale":2137,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":667}	127.0.0.1	Se creó el vale de combustible N.º 2137	2026-10-22 12:56:38	2026-10-22 12:56:38
1523	3	CREAR	vales_combustible	668	\N	{"numero_vale":2138,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":668}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1524	3	CREAR	vales_combustible	668	\N	{"numero_vale":2138,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":668}	127.0.0.1	Se creó el vale de combustible N.º 2138	2026-10-22 12:56:38	2026-10-22 12:56:38
1525	3	CREAR	vales_combustible	669	\N	{"numero_vale":2139,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":669}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1526	3	CREAR	vales_combustible	669	\N	{"numero_vale":2139,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":669}	127.0.0.1	Se creó el vale de combustible N.º 2139	2026-10-22 12:56:38	2026-10-22 12:56:38
1527	3	CREAR	vales_combustible	670	\N	{"numero_vale":2140,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":670}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1528	3	CREAR	vales_combustible	670	\N	{"numero_vale":2140,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":670}	127.0.0.1	Se creó el vale de combustible N.º 2140	2026-10-22 12:56:38	2026-10-22 12:56:38
1529	3	CREAR	vales_combustible	671	\N	{"numero_vale":2141,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":671}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1530	3	CREAR	vales_combustible	671	\N	{"numero_vale":2141,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":671}	127.0.0.1	Se creó el vale de combustible N.º 2141	2026-10-22 12:56:38	2026-10-22 12:56:38
1531	3	CREAR	vales_combustible	672	\N	{"numero_vale":2142,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":672}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1532	3	CREAR	vales_combustible	672	\N	{"numero_vale":2142,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":672}	127.0.0.1	Se creó el vale de combustible N.º 2142	2026-10-22 12:56:38	2026-10-22 12:56:38
1533	3	CREAR	vales_combustible	673	\N	{"numero_vale":2143,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":673}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1534	3	CREAR	vales_combustible	673	\N	{"numero_vale":2143,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":673}	127.0.0.1	Se creó el vale de combustible N.º 2143	2026-10-22 12:56:38	2026-10-22 12:56:38
1535	3	CREAR	vales_combustible	674	\N	{"numero_vale":2144,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":674}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1536	3	CREAR	vales_combustible	674	\N	{"numero_vale":2144,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":674}	127.0.0.1	Se creó el vale de combustible N.º 2144	2026-10-22 12:56:38	2026-10-22 12:56:38
1537	3	CREAR	vales_combustible	675	\N	{"numero_vale":2145,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":675}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1538	3	CREAR	vales_combustible	675	\N	{"numero_vale":2145,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":675}	127.0.0.1	Se creó el vale de combustible N.º 2145	2026-10-22 12:56:38	2026-10-22 12:56:38
1539	3	CREAR	vales_combustible	676	\N	{"numero_vale":2146,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":676}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1540	3	CREAR	vales_combustible	676	\N	{"numero_vale":2146,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":676}	127.0.0.1	Se creó el vale de combustible N.º 2146	2026-10-22 12:56:38	2026-10-22 12:56:38
1541	3	CREAR	vales_combustible	677	\N	{"numero_vale":2147,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":677}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1542	3	CREAR	vales_combustible	677	\N	{"numero_vale":2147,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":677}	127.0.0.1	Se creó el vale de combustible N.º 2147	2026-10-22 12:56:38	2026-10-22 12:56:38
1543	3	CREAR	vales_combustible	678	\N	{"numero_vale":2148,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":678}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1544	3	CREAR	vales_combustible	678	\N	{"numero_vale":2148,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":678}	127.0.0.1	Se creó el vale de combustible N.º 2148	2026-10-22 12:56:38	2026-10-22 12:56:38
1545	3	CREAR	vales_combustible	679	\N	{"numero_vale":2149,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":679}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1546	3	CREAR	vales_combustible	679	\N	{"numero_vale":2149,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":679}	127.0.0.1	Se creó el vale de combustible N.º 2149	2026-10-22 12:56:38	2026-10-22 12:56:38
1547	3	CREAR	vales_combustible	680	\N	{"numero_vale":2150,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":680}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1548	3	CREAR	vales_combustible	680	\N	{"numero_vale":2150,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":680}	127.0.0.1	Se creó el vale de combustible N.º 2150	2026-10-22 12:56:38	2026-10-22 12:56:38
1549	3	CREAR	vales_combustible	681	\N	{"numero_vale":2151,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":681}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1550	3	CREAR	vales_combustible	681	\N	{"numero_vale":2151,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":681}	127.0.0.1	Se creó el vale de combustible N.º 2151	2026-10-22 12:56:38	2026-10-22 12:56:38
1551	3	CREAR	vales_combustible	682	\N	{"numero_vale":2152,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":682}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1552	3	CREAR	vales_combustible	682	\N	{"numero_vale":2152,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":682}	127.0.0.1	Se creó el vale de combustible N.º 2152	2026-10-22 12:56:38	2026-10-22 12:56:38
1553	3	CREAR	vales_combustible	683	\N	{"numero_vale":2153,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":683}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1554	3	CREAR	vales_combustible	683	\N	{"numero_vale":2153,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":683}	127.0.0.1	Se creó el vale de combustible N.º 2153	2026-10-22 12:56:38	2026-10-22 12:56:38
1555	3	CREAR	vales_combustible	684	\N	{"numero_vale":2154,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":684}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1556	3	CREAR	vales_combustible	684	\N	{"numero_vale":2154,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":684}	127.0.0.1	Se creó el vale de combustible N.º 2154	2026-10-22 12:56:38	2026-10-22 12:56:38
1557	3	CREAR	vales_combustible	685	\N	{"numero_vale":2155,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":685}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1558	3	CREAR	vales_combustible	685	\N	{"numero_vale":2155,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":685}	127.0.0.1	Se creó el vale de combustible N.º 2155	2026-10-22 12:56:38	2026-10-22 12:56:38
1559	3	CREAR	vales_combustible	686	\N	{"numero_vale":2156,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":686}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1560	3	CREAR	vales_combustible	686	\N	{"numero_vale":2156,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":686}	127.0.0.1	Se creó el vale de combustible N.º 2156	2026-10-22 12:56:38	2026-10-22 12:56:38
1561	3	CREAR	vales_combustible	687	\N	{"numero_vale":2157,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":687}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1562	3	CREAR	vales_combustible	687	\N	{"numero_vale":2157,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":687}	127.0.0.1	Se creó el vale de combustible N.º 2157	2026-10-22 12:56:38	2026-10-22 12:56:38
1563	3	CREAR	vales_combustible	688	\N	{"numero_vale":2158,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":688}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1564	3	CREAR	vales_combustible	688	\N	{"numero_vale":2158,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":688}	127.0.0.1	Se creó el vale de combustible N.º 2158	2026-10-22 12:56:38	2026-10-22 12:56:38
1565	3	CREAR	vales_combustible	689	\N	{"numero_vale":2159,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":689}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1566	3	CREAR	vales_combustible	689	\N	{"numero_vale":2159,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":689}	127.0.0.1	Se creó el vale de combustible N.º 2159	2026-10-22 12:56:38	2026-10-22 12:56:38
1567	3	CREAR	vales_combustible	690	\N	{"numero_vale":2160,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":690}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1568	3	CREAR	vales_combustible	690	\N	{"numero_vale":2160,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":690}	127.0.0.1	Se creó el vale de combustible N.º 2160	2026-10-22 12:56:38	2026-10-22 12:56:38
1569	3	CREAR	vales_combustible	691	\N	{"numero_vale":2161,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":691}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1570	3	CREAR	vales_combustible	691	\N	{"numero_vale":2161,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":691}	127.0.0.1	Se creó el vale de combustible N.º 2161	2026-10-22 12:56:38	2026-10-22 12:56:38
1571	3	CREAR	vales_combustible	692	\N	{"numero_vale":2162,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":692}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1572	3	CREAR	vales_combustible	692	\N	{"numero_vale":2162,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":692}	127.0.0.1	Se creó el vale de combustible N.º 2162	2026-10-22 12:56:38	2026-10-22 12:56:38
1573	3	CREAR	vales_combustible	693	\N	{"numero_vale":2163,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":693}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1574	3	CREAR	vales_combustible	693	\N	{"numero_vale":2163,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":693}	127.0.0.1	Se creó el vale de combustible N.º 2163	2026-10-22 12:56:38	2026-10-22 12:56:38
1575	3	CREAR	vales_combustible	694	\N	{"numero_vale":2164,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":694}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1576	3	CREAR	vales_combustible	694	\N	{"numero_vale":2164,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":694}	127.0.0.1	Se creó el vale de combustible N.º 2164	2026-10-22 12:56:38	2026-10-22 12:56:38
1577	3	CREAR	vales_combustible	695	\N	{"numero_vale":2165,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":695}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1578	3	CREAR	vales_combustible	695	\N	{"numero_vale":2165,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":695}	127.0.0.1	Se creó el vale de combustible N.º 2165	2026-10-22 12:56:38	2026-10-22 12:56:38
1579	3	CREAR	vales_combustible	696	\N	{"numero_vale":2166,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":696}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1580	3	CREAR	vales_combustible	696	\N	{"numero_vale":2166,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":696}	127.0.0.1	Se creó el vale de combustible N.º 2166	2026-10-22 12:56:38	2026-10-22 12:56:38
1581	3	CREAR	vales_combustible	697	\N	{"numero_vale":2167,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":697}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1582	3	CREAR	vales_combustible	697	\N	{"numero_vale":2167,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":697}	127.0.0.1	Se creó el vale de combustible N.º 2167	2026-10-22 12:56:38	2026-10-22 12:56:38
1583	3	CREAR	vales_combustible	698	\N	{"numero_vale":2168,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":698}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1584	3	CREAR	vales_combustible	698	\N	{"numero_vale":2168,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":698}	127.0.0.1	Se creó el vale de combustible N.º 2168	2026-10-22 12:56:38	2026-10-22 12:56:38
1585	3	CREAR	vales_combustible	699	\N	{"numero_vale":2169,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":699}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1586	3	CREAR	vales_combustible	699	\N	{"numero_vale":2169,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":699}	127.0.0.1	Se creó el vale de combustible N.º 2169	2026-10-22 12:56:38	2026-10-22 12:56:38
1587	3	CREAR	vales_combustible	700	\N	{"numero_vale":2170,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":700}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1588	3	CREAR	vales_combustible	700	\N	{"numero_vale":2170,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":700}	127.0.0.1	Se creó el vale de combustible N.º 2170	2026-10-22 12:56:38	2026-10-22 12:56:38
1589	3	CREAR	vales_combustible	701	\N	{"numero_vale":2171,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":701}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1590	3	CREAR	vales_combustible	701	\N	{"numero_vale":2171,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":701}	127.0.0.1	Se creó el vale de combustible N.º 2171	2026-10-22 12:56:38	2026-10-22 12:56:38
1591	3	CREAR	vales_combustible	702	\N	{"numero_vale":2172,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":702}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1592	3	CREAR	vales_combustible	702	\N	{"numero_vale":2172,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":702}	127.0.0.1	Se creó el vale de combustible N.º 2172	2026-10-22 12:56:38	2026-10-22 12:56:38
1593	3	CREAR	vales_combustible	703	\N	{"numero_vale":2173,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":703}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1594	3	CREAR	vales_combustible	703	\N	{"numero_vale":2173,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":703}	127.0.0.1	Se creó el vale de combustible N.º 2173	2026-10-22 12:56:38	2026-10-22 12:56:38
1595	3	CREAR	vales_combustible	704	\N	{"numero_vale":2174,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":704}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1596	3	CREAR	vales_combustible	704	\N	{"numero_vale":2174,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":704}	127.0.0.1	Se creó el vale de combustible N.º 2174	2026-10-22 12:56:38	2026-10-22 12:56:38
1597	3	CREAR	vales_combustible	705	\N	{"numero_vale":2175,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":705}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1598	3	CREAR	vales_combustible	705	\N	{"numero_vale":2175,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":705}	127.0.0.1	Se creó el vale de combustible N.º 2175	2026-10-22 12:56:38	2026-10-22 12:56:38
1599	3	CREAR	vales_combustible	706	\N	{"numero_vale":2176,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":706}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1600	3	CREAR	vales_combustible	706	\N	{"numero_vale":2176,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":706}	127.0.0.1	Se creó el vale de combustible N.º 2176	2026-10-22 12:56:38	2026-10-22 12:56:38
1601	3	CREAR	vales_combustible	707	\N	{"numero_vale":2177,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":707}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1602	3	CREAR	vales_combustible	707	\N	{"numero_vale":2177,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":707}	127.0.0.1	Se creó el vale de combustible N.º 2177	2026-10-22 12:56:38	2026-10-22 12:56:38
1603	3	CREAR	vales_combustible	708	\N	{"numero_vale":2178,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":708}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1604	3	CREAR	vales_combustible	708	\N	{"numero_vale":2178,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":708}	127.0.0.1	Se creó el vale de combustible N.º 2178	2026-10-22 12:56:38	2026-10-22 12:56:38
1605	3	CREAR	vales_combustible	709	\N	{"numero_vale":2179,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":709}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1606	3	CREAR	vales_combustible	709	\N	{"numero_vale":2179,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":709}	127.0.0.1	Se creó el vale de combustible N.º 2179	2026-10-22 12:56:38	2026-10-22 12:56:38
1607	3	CREAR	vales_combustible	710	\N	{"numero_vale":2180,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":710}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1608	3	CREAR	vales_combustible	710	\N	{"numero_vale":2180,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":710}	127.0.0.1	Se creó el vale de combustible N.º 2180	2026-10-22 12:56:38	2026-10-22 12:56:38
1609	3	CREAR	vales_combustible	711	\N	{"numero_vale":2181,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":711}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1610	3	CREAR	vales_combustible	711	\N	{"numero_vale":2181,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":711}	127.0.0.1	Se creó el vale de combustible N.º 2181	2026-10-22 12:56:38	2026-10-22 12:56:38
1611	3	CREAR	vales_combustible	712	\N	{"numero_vale":2182,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":712}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1612	3	CREAR	vales_combustible	712	\N	{"numero_vale":2182,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":712}	127.0.0.1	Se creó el vale de combustible N.º 2182	2026-10-22 12:56:38	2026-10-22 12:56:38
1613	3	CREAR	vales_combustible	713	\N	{"numero_vale":2183,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":713}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1614	3	CREAR	vales_combustible	713	\N	{"numero_vale":2183,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":713}	127.0.0.1	Se creó el vale de combustible N.º 2183	2026-10-22 12:56:38	2026-10-22 12:56:38
1615	3	CREAR	vales_combustible	714	\N	{"numero_vale":2184,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":714}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1616	3	CREAR	vales_combustible	714	\N	{"numero_vale":2184,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":714}	127.0.0.1	Se creó el vale de combustible N.º 2184	2026-10-22 12:56:38	2026-10-22 12:56:38
1617	3	CREAR	vales_combustible	715	\N	{"numero_vale":2185,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":715}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1618	3	CREAR	vales_combustible	715	\N	{"numero_vale":2185,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":715}	127.0.0.1	Se creó el vale de combustible N.º 2185	2026-10-22 12:56:38	2026-10-22 12:56:38
1619	3	CREAR	vales_combustible	716	\N	{"numero_vale":2186,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":716}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1620	3	CREAR	vales_combustible	716	\N	{"numero_vale":2186,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":716}	127.0.0.1	Se creó el vale de combustible N.º 2186	2026-10-22 12:56:38	2026-10-22 12:56:38
1621	3	CREAR	vales_combustible	717	\N	{"numero_vale":2187,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":717}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1622	3	CREAR	vales_combustible	717	\N	{"numero_vale":2187,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":717}	127.0.0.1	Se creó el vale de combustible N.º 2187	2026-10-22 12:56:38	2026-10-22 12:56:38
1623	3	CREAR	vales_combustible	718	\N	{"numero_vale":2188,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":718}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1624	3	CREAR	vales_combustible	718	\N	{"numero_vale":2188,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":718}	127.0.0.1	Se creó el vale de combustible N.º 2188	2026-10-22 12:56:38	2026-10-22 12:56:38
1625	3	CREAR	vales_combustible	719	\N	{"numero_vale":2189,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":719}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1626	3	CREAR	vales_combustible	719	\N	{"numero_vale":2189,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":719}	127.0.0.1	Se creó el vale de combustible N.º 2189	2026-10-22 12:56:38	2026-10-22 12:56:38
1627	3	CREAR	vales_combustible	720	\N	{"numero_vale":2190,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":720}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1628	3	CREAR	vales_combustible	720	\N	{"numero_vale":2190,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":720}	127.0.0.1	Se creó el vale de combustible N.º 2190	2026-10-22 12:56:38	2026-10-22 12:56:38
1629	3	CREAR	vales_combustible	721	\N	{"numero_vale":2191,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":721}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1630	3	CREAR	vales_combustible	721	\N	{"numero_vale":2191,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":721}	127.0.0.1	Se creó el vale de combustible N.º 2191	2026-10-22 12:56:38	2026-10-22 12:56:38
1631	3	CREAR	vales_combustible	722	\N	{"numero_vale":2192,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":722}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1632	3	CREAR	vales_combustible	722	\N	{"numero_vale":2192,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":722}	127.0.0.1	Se creó el vale de combustible N.º 2192	2026-10-22 12:56:38	2026-10-22 12:56:38
1633	3	CREAR	vales_combustible	723	\N	{"numero_vale":2193,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":723}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1634	3	CREAR	vales_combustible	723	\N	{"numero_vale":2193,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":723}	127.0.0.1	Se creó el vale de combustible N.º 2193	2026-10-22 12:56:38	2026-10-22 12:56:38
1635	3	CREAR	vales_combustible	724	\N	{"numero_vale":2194,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":724}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1636	3	CREAR	vales_combustible	724	\N	{"numero_vale":2194,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":724}	127.0.0.1	Se creó el vale de combustible N.º 2194	2026-10-22 12:56:38	2026-10-22 12:56:38
1637	3	CREAR	vales_combustible	725	\N	{"numero_vale":2195,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":725}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1638	3	CREAR	vales_combustible	725	\N	{"numero_vale":2195,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":725}	127.0.0.1	Se creó el vale de combustible N.º 2195	2026-10-22 12:56:38	2026-10-22 12:56:38
1639	3	CREAR	vales_combustible	726	\N	{"numero_vale":2196,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":726}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1640	3	CREAR	vales_combustible	726	\N	{"numero_vale":2196,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":726}	127.0.0.1	Se creó el vale de combustible N.º 2196	2026-10-22 12:56:38	2026-10-22 12:56:38
1641	3	CREAR	vales_combustible	727	\N	{"numero_vale":2197,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":727}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1642	3	CREAR	vales_combustible	727	\N	{"numero_vale":2197,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":727}	127.0.0.1	Se creó el vale de combustible N.º 2197	2026-10-22 12:56:38	2026-10-22 12:56:38
1643	3	CREAR	vales_combustible	728	\N	{"numero_vale":2198,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":728}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1644	3	CREAR	vales_combustible	728	\N	{"numero_vale":2198,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":728}	127.0.0.1	Se creó el vale de combustible N.º 2198	2026-10-22 12:56:38	2026-10-22 12:56:38
1645	3	CREAR	vales_combustible	729	\N	{"numero_vale":2199,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":729}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1646	3	CREAR	vales_combustible	729	\N	{"numero_vale":2199,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":729}	127.0.0.1	Se creó el vale de combustible N.º 2199	2026-10-22 12:56:38	2026-10-22 12:56:38
1647	3	CREAR	vales_combustible	730	\N	{"numero_vale":2200,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":730}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1648	3	CREAR	vales_combustible	730	\N	{"numero_vale":2200,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":730}	127.0.0.1	Se creó el vale de combustible N.º 2200	2026-10-22 12:56:38	2026-10-22 12:56:38
1649	3	CREAR	vales_combustible	731	\N	{"numero_vale":2201,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":731}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1650	3	CREAR	vales_combustible	731	\N	{"numero_vale":2201,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":731}	127.0.0.1	Se creó el vale de combustible N.º 2201	2026-10-22 12:56:38	2026-10-22 12:56:38
1651	3	CREAR	vales_combustible	732	\N	{"numero_vale":2202,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":732}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1652	3	CREAR	vales_combustible	732	\N	{"numero_vale":2202,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":732}	127.0.0.1	Se creó el vale de combustible N.º 2202	2026-10-22 12:56:38	2026-10-22 12:56:38
1653	3	CREAR	vales_combustible	733	\N	{"numero_vale":2203,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":733}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1654	3	CREAR	vales_combustible	733	\N	{"numero_vale":2203,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":733}	127.0.0.1	Se creó el vale de combustible N.º 2203	2026-10-22 12:56:38	2026-10-22 12:56:38
1655	3	CREAR	vales_combustible	734	\N	{"numero_vale":2204,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":734}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1656	3	CREAR	vales_combustible	734	\N	{"numero_vale":2204,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":734}	127.0.0.1	Se creó el vale de combustible N.º 2204	2026-10-22 12:56:38	2026-10-22 12:56:38
1657	3	CREAR	vales_combustible	735	\N	{"numero_vale":2205,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":735}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1658	3	CREAR	vales_combustible	735	\N	{"numero_vale":2205,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":735}	127.0.0.1	Se creó el vale de combustible N.º 2205	2026-10-22 12:56:38	2026-10-22 12:56:38
1659	3	CREAR	vales_combustible	736	\N	{"numero_vale":2206,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":736}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1660	3	CREAR	vales_combustible	736	\N	{"numero_vale":2206,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":736}	127.0.0.1	Se creó el vale de combustible N.º 2206	2026-10-22 12:56:38	2026-10-22 12:56:38
1661	3	CREAR	vales_combustible	737	\N	{"numero_vale":2207,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":737}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1662	3	CREAR	vales_combustible	737	\N	{"numero_vale":2207,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":737}	127.0.0.1	Se creó el vale de combustible N.º 2207	2026-10-22 12:56:38	2026-10-22 12:56:38
1663	3	CREAR	vales_combustible	738	\N	{"numero_vale":2208,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":738}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1664	3	CREAR	vales_combustible	738	\N	{"numero_vale":2208,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":738}	127.0.0.1	Se creó el vale de combustible N.º 2208	2026-10-22 12:56:38	2026-10-22 12:56:38
1665	3	CREAR	vales_combustible	739	\N	{"numero_vale":2209,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":739}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1666	3	CREAR	vales_combustible	739	\N	{"numero_vale":2209,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":739}	127.0.0.1	Se creó el vale de combustible N.º 2209	2026-10-22 12:56:38	2026-10-22 12:56:38
1667	3	CREAR	vales_combustible	740	\N	{"numero_vale":2210,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":740}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1668	3	CREAR	vales_combustible	740	\N	{"numero_vale":2210,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":740}	127.0.0.1	Se creó el vale de combustible N.º 2210	2026-10-22 12:56:38	2026-10-22 12:56:38
1669	3	CREAR	vales_combustible	741	\N	{"numero_vale":2211,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":741}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1670	3	CREAR	vales_combustible	741	\N	{"numero_vale":2211,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":741}	127.0.0.1	Se creó el vale de combustible N.º 2211	2026-10-22 12:56:38	2026-10-22 12:56:38
1671	3	CREAR	vales_combustible	742	\N	{"numero_vale":2212,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":742}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1672	3	CREAR	vales_combustible	742	\N	{"numero_vale":2212,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":742}	127.0.0.1	Se creó el vale de combustible N.º 2212	2026-10-22 12:56:38	2026-10-22 12:56:38
1673	3	CREAR	vales_combustible	743	\N	{"numero_vale":2213,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":743}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1674	3	CREAR	vales_combustible	743	\N	{"numero_vale":2213,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":743}	127.0.0.1	Se creó el vale de combustible N.º 2213	2026-10-22 12:56:38	2026-10-22 12:56:38
1675	3	CREAR	vales_combustible	744	\N	{"numero_vale":2214,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":744}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1676	3	CREAR	vales_combustible	744	\N	{"numero_vale":2214,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":744}	127.0.0.1	Se creó el vale de combustible N.º 2214	2026-10-22 12:56:38	2026-10-22 12:56:38
1677	3	CREAR	vales_combustible	745	\N	{"numero_vale":2215,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":745}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1678	3	CREAR	vales_combustible	745	\N	{"numero_vale":2215,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":745}	127.0.0.1	Se creó el vale de combustible N.º 2215	2026-10-22 12:56:38	2026-10-22 12:56:38
1679	3	CREAR	vales_combustible	746	\N	{"numero_vale":2216,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":746}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1680	3	CREAR	vales_combustible	746	\N	{"numero_vale":2216,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":746}	127.0.0.1	Se creó el vale de combustible N.º 2216	2026-10-22 12:56:38	2026-10-22 12:56:38
1681	3	CREAR	vales_combustible	747	\N	{"numero_vale":2217,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":747}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1682	3	CREAR	vales_combustible	747	\N	{"numero_vale":2217,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":747}	127.0.0.1	Se creó el vale de combustible N.º 2217	2026-10-22 12:56:38	2026-10-22 12:56:38
1683	3	CREAR	vales_combustible	748	\N	{"numero_vale":2218,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":748}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1684	3	CREAR	vales_combustible	748	\N	{"numero_vale":2218,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":748}	127.0.0.1	Se creó el vale de combustible N.º 2218	2026-10-22 12:56:38	2026-10-22 12:56:38
1685	3	CREAR	vales_combustible	749	\N	{"numero_vale":2219,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":749}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1686	3	CREAR	vales_combustible	749	\N	{"numero_vale":2219,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":749}	127.0.0.1	Se creó el vale de combustible N.º 2219	2026-10-22 12:56:38	2026-10-22 12:56:38
1687	3	CREAR	vales_combustible	750	\N	{"numero_vale":2220,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":750}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1688	3	CREAR	vales_combustible	750	\N	{"numero_vale":2220,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":750}	127.0.0.1	Se creó el vale de combustible N.º 2220	2026-10-22 12:56:38	2026-10-22 12:56:38
1689	3	CREAR	vales_combustible	751	\N	{"numero_vale":2221,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":751}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1690	3	CREAR	vales_combustible	751	\N	{"numero_vale":2221,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":751}	127.0.0.1	Se creó el vale de combustible N.º 2221	2026-10-22 12:56:38	2026-10-22 12:56:38
1691	3	CREAR	vales_combustible	752	\N	{"numero_vale":2222,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":752}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1692	3	CREAR	vales_combustible	752	\N	{"numero_vale":2222,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":752}	127.0.0.1	Se creó el vale de combustible N.º 2222	2026-10-22 12:56:38	2026-10-22 12:56:38
1693	3	CREAR	vales_combustible	753	\N	{"numero_vale":2223,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":753}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1694	3	CREAR	vales_combustible	753	\N	{"numero_vale":2223,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":753}	127.0.0.1	Se creó el vale de combustible N.º 2223	2026-10-22 12:56:38	2026-10-22 12:56:38
1695	3	CREAR	vales_combustible	754	\N	{"numero_vale":2224,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":754}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1696	3	CREAR	vales_combustible	754	\N	{"numero_vale":2224,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":754}	127.0.0.1	Se creó el vale de combustible N.º 2224	2026-10-22 12:56:38	2026-10-22 12:56:38
1697	3	CREAR	vales_combustible	755	\N	{"numero_vale":2225,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":755}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1698	3	CREAR	vales_combustible	755	\N	{"numero_vale":2225,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":755}	127.0.0.1	Se creó el vale de combustible N.º 2225	2026-10-22 12:56:38	2026-10-22 12:56:38
1699	3	CREAR	vales_combustible	756	\N	{"numero_vale":2226,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":756}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1700	3	CREAR	vales_combustible	756	\N	{"numero_vale":2226,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":756}	127.0.0.1	Se creó el vale de combustible N.º 2226	2026-10-22 12:56:38	2026-10-22 12:56:38
1701	3	CREAR	vales_combustible	757	\N	{"numero_vale":2227,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":757}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1702	3	CREAR	vales_combustible	757	\N	{"numero_vale":2227,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":757}	127.0.0.1	Se creó el vale de combustible N.º 2227	2026-10-22 12:56:38	2026-10-22 12:56:38
1703	3	CREAR	vales_combustible	758	\N	{"numero_vale":2228,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":758}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1704	3	CREAR	vales_combustible	758	\N	{"numero_vale":2228,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":758}	127.0.0.1	Se creó el vale de combustible N.º 2228	2026-10-22 12:56:38	2026-10-22 12:56:38
1705	3	CREAR	vales_combustible	759	\N	{"numero_vale":2229,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":759}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1706	3	CREAR	vales_combustible	759	\N	{"numero_vale":2229,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":759}	127.0.0.1	Se creó el vale de combustible N.º 2229	2026-10-22 12:56:38	2026-10-22 12:56:38
1707	3	CREAR	vales_combustible	760	\N	{"numero_vale":2230,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":760}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1708	3	CREAR	vales_combustible	760	\N	{"numero_vale":2230,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":760}	127.0.0.1	Se creó el vale de combustible N.º 2230	2026-10-22 12:56:38	2026-10-22 12:56:38
1709	3	CREAR	vales_combustible	761	\N	{"numero_vale":2231,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":761}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1710	3	CREAR	vales_combustible	761	\N	{"numero_vale":2231,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":761}	127.0.0.1	Se creó el vale de combustible N.º 2231	2026-10-22 12:56:38	2026-10-22 12:56:38
1711	3	CREAR	vales_combustible	762	\N	{"numero_vale":2232,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":762}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1712	3	CREAR	vales_combustible	762	\N	{"numero_vale":2232,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":762}	127.0.0.1	Se creó el vale de combustible N.º 2232	2026-10-22 12:56:38	2026-10-22 12:56:38
1713	3	CREAR	vales_combustible	763	\N	{"numero_vale":2233,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":763}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1714	3	CREAR	vales_combustible	763	\N	{"numero_vale":2233,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":763}	127.0.0.1	Se creó el vale de combustible N.º 2233	2026-10-22 12:56:38	2026-10-22 12:56:38
1715	3	CREAR	vales_combustible	764	\N	{"numero_vale":2234,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":764}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1716	3	CREAR	vales_combustible	764	\N	{"numero_vale":2234,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":764}	127.0.0.1	Se creó el vale de combustible N.º 2234	2026-10-22 12:56:38	2026-10-22 12:56:38
1717	3	CREAR	vales_combustible	765	\N	{"numero_vale":2235,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":765}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1718	3	CREAR	vales_combustible	765	\N	{"numero_vale":2235,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":765}	127.0.0.1	Se creó el vale de combustible N.º 2235	2026-10-22 12:56:38	2026-10-22 12:56:38
1719	3	CREAR	vales_combustible	766	\N	{"numero_vale":2236,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":766}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1720	3	CREAR	vales_combustible	766	\N	{"numero_vale":2236,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":766}	127.0.0.1	Se creó el vale de combustible N.º 2236	2026-10-22 12:56:38	2026-10-22 12:56:38
1721	3	CREAR	vales_combustible	767	\N	{"numero_vale":2237,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":767}	127.0.0.1	Registro creado	2026-10-22 12:56:38	2026-10-22 12:56:38
1722	3	CREAR	vales_combustible	767	\N	{"numero_vale":2237,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:38","created_at":"2026-10-22 12:56:38","id":767}	127.0.0.1	Se creó el vale de combustible N.º 2237	2026-10-22 12:56:39	2026-10-22 12:56:39
1723	3	CREAR	vales_combustible	768	\N	{"numero_vale":2238,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":768}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1724	3	CREAR	vales_combustible	768	\N	{"numero_vale":2238,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":768}	127.0.0.1	Se creó el vale de combustible N.º 2238	2026-10-22 12:56:39	2026-10-22 12:56:39
1725	3	CREAR	vales_combustible	769	\N	{"numero_vale":2239,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":769}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1726	3	CREAR	vales_combustible	769	\N	{"numero_vale":2239,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":769}	127.0.0.1	Se creó el vale de combustible N.º 2239	2026-10-22 12:56:39	2026-10-22 12:56:39
1727	3	CREAR	vales_combustible	770	\N	{"numero_vale":2240,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":770}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1728	3	CREAR	vales_combustible	770	\N	{"numero_vale":2240,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":770}	127.0.0.1	Se creó el vale de combustible N.º 2240	2026-10-22 12:56:39	2026-10-22 12:56:39
1729	3	CREAR	vales_combustible	771	\N	{"numero_vale":2241,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":771}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1730	3	CREAR	vales_combustible	771	\N	{"numero_vale":2241,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":771}	127.0.0.1	Se creó el vale de combustible N.º 2241	2026-10-22 12:56:39	2026-10-22 12:56:39
1731	3	CREAR	vales_combustible	772	\N	{"numero_vale":2242,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":772}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1732	3	CREAR	vales_combustible	772	\N	{"numero_vale":2242,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":772}	127.0.0.1	Se creó el vale de combustible N.º 2242	2026-10-22 12:56:39	2026-10-22 12:56:39
1733	3	CREAR	vales_combustible	773	\N	{"numero_vale":2243,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":773}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1734	3	CREAR	vales_combustible	773	\N	{"numero_vale":2243,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":773}	127.0.0.1	Se creó el vale de combustible N.º 2243	2026-10-22 12:56:39	2026-10-22 12:56:39
1735	3	CREAR	vales_combustible	774	\N	{"numero_vale":2244,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":774}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1736	3	CREAR	vales_combustible	774	\N	{"numero_vale":2244,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":774}	127.0.0.1	Se creó el vale de combustible N.º 2244	2026-10-22 12:56:39	2026-10-22 12:56:39
1737	3	CREAR	vales_combustible	775	\N	{"numero_vale":2245,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":775}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1738	3	CREAR	vales_combustible	775	\N	{"numero_vale":2245,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":775}	127.0.0.1	Se creó el vale de combustible N.º 2245	2026-10-22 12:56:39	2026-10-22 12:56:39
1739	3	CREAR	vales_combustible	776	\N	{"numero_vale":2246,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":776}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1740	3	CREAR	vales_combustible	776	\N	{"numero_vale":2246,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":776}	127.0.0.1	Se creó el vale de combustible N.º 2246	2026-10-22 12:56:39	2026-10-22 12:56:39
1741	3	CREAR	vales_combustible	777	\N	{"numero_vale":2247,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":777}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1742	3	CREAR	vales_combustible	777	\N	{"numero_vale":2247,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":777}	127.0.0.1	Se creó el vale de combustible N.º 2247	2026-10-22 12:56:39	2026-10-22 12:56:39
1743	3	CREAR	vales_combustible	778	\N	{"numero_vale":2248,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":778}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1744	3	CREAR	vales_combustible	778	\N	{"numero_vale":2248,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":778}	127.0.0.1	Se creó el vale de combustible N.º 2248	2026-10-22 12:56:39	2026-10-22 12:56:39
1745	3	CREAR	vales_combustible	779	\N	{"numero_vale":2249,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":779}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1746	3	CREAR	vales_combustible	779	\N	{"numero_vale":2249,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":779}	127.0.0.1	Se creó el vale de combustible N.º 2249	2026-10-22 12:56:39	2026-10-22 12:56:39
1747	3	CREAR	vales_combustible	780	\N	{"numero_vale":2250,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":780}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1748	3	CREAR	vales_combustible	780	\N	{"numero_vale":2250,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":780}	127.0.0.1	Se creó el vale de combustible N.º 2250	2026-10-22 12:56:39	2026-10-22 12:56:39
1749	3	CREAR	vales_combustible	781	\N	{"numero_vale":2251,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":781}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1750	3	CREAR	vales_combustible	781	\N	{"numero_vale":2251,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":781}	127.0.0.1	Se creó el vale de combustible N.º 2251	2026-10-22 12:56:39	2026-10-22 12:56:39
1751	3	CREAR	vales_combustible	782	\N	{"numero_vale":2252,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":782}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1752	3	CREAR	vales_combustible	782	\N	{"numero_vale":2252,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":782}	127.0.0.1	Se creó el vale de combustible N.º 2252	2026-10-22 12:56:39	2026-10-22 12:56:39
1753	3	CREAR	vales_combustible	783	\N	{"numero_vale":2253,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":783}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1754	3	CREAR	vales_combustible	783	\N	{"numero_vale":2253,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":783}	127.0.0.1	Se creó el vale de combustible N.º 2253	2026-10-22 12:56:39	2026-10-22 12:56:39
1755	3	CREAR	vales_combustible	784	\N	{"numero_vale":2254,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":784}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1756	3	CREAR	vales_combustible	784	\N	{"numero_vale":2254,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":784}	127.0.0.1	Se creó el vale de combustible N.º 2254	2026-10-22 12:56:39	2026-10-22 12:56:39
1757	3	CREAR	vales_combustible	785	\N	{"numero_vale":2255,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":785}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1758	3	CREAR	vales_combustible	785	\N	{"numero_vale":2255,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":785}	127.0.0.1	Se creó el vale de combustible N.º 2255	2026-10-22 12:56:39	2026-10-22 12:56:39
1759	3	CREAR	vales_combustible	786	\N	{"numero_vale":2256,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":786}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1760	3	CREAR	vales_combustible	786	\N	{"numero_vale":2256,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":786}	127.0.0.1	Se creó el vale de combustible N.º 2256	2026-10-22 12:56:39	2026-10-22 12:56:39
1761	3	CREAR	vales_combustible	787	\N	{"numero_vale":2257,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":787}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1762	3	CREAR	vales_combustible	787	\N	{"numero_vale":2257,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":787}	127.0.0.1	Se creó el vale de combustible N.º 2257	2026-10-22 12:56:39	2026-10-22 12:56:39
1763	3	CREAR	vales_combustible	788	\N	{"numero_vale":2258,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":788}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1764	3	CREAR	vales_combustible	788	\N	{"numero_vale":2258,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":788}	127.0.0.1	Se creó el vale de combustible N.º 2258	2026-10-22 12:56:39	2026-10-22 12:56:39
1765	3	CREAR	vales_combustible	789	\N	{"numero_vale":2259,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":789}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1766	3	CREAR	vales_combustible	789	\N	{"numero_vale":2259,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":789}	127.0.0.1	Se creó el vale de combustible N.º 2259	2026-10-22 12:56:39	2026-10-22 12:56:39
1767	3	CREAR	vales_combustible	790	\N	{"numero_vale":2260,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":790}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1768	3	CREAR	vales_combustible	790	\N	{"numero_vale":2260,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":790}	127.0.0.1	Se creó el vale de combustible N.º 2260	2026-10-22 12:56:39	2026-10-22 12:56:39
1769	3	CREAR	vales_combustible	791	\N	{"numero_vale":2261,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":791}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1770	3	CREAR	vales_combustible	791	\N	{"numero_vale":2261,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":791}	127.0.0.1	Se creó el vale de combustible N.º 2261	2026-10-22 12:56:39	2026-10-22 12:56:39
1771	3	CREAR	vales_combustible	792	\N	{"numero_vale":2262,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":792}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1772	3	CREAR	vales_combustible	792	\N	{"numero_vale":2262,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":792}	127.0.0.1	Se creó el vale de combustible N.º 2262	2026-10-22 12:56:39	2026-10-22 12:56:39
1773	3	CREAR	vales_combustible	793	\N	{"numero_vale":2263,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":793}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1774	3	CREAR	vales_combustible	793	\N	{"numero_vale":2263,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":793}	127.0.0.1	Se creó el vale de combustible N.º 2263	2026-10-22 12:56:39	2026-10-22 12:56:39
1775	3	CREAR	vales_combustible	794	\N	{"numero_vale":2264,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":794}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1776	3	CREAR	vales_combustible	794	\N	{"numero_vale":2264,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":794}	127.0.0.1	Se creó el vale de combustible N.º 2264	2026-10-22 12:56:39	2026-10-22 12:56:39
1777	3	CREAR	vales_combustible	795	\N	{"numero_vale":2265,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":795}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1778	3	CREAR	vales_combustible	795	\N	{"numero_vale":2265,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":795}	127.0.0.1	Se creó el vale de combustible N.º 2265	2026-10-22 12:56:39	2026-10-22 12:56:39
1779	3	CREAR	vales_combustible	796	\N	{"numero_vale":2266,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":796}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1780	3	CREAR	vales_combustible	796	\N	{"numero_vale":2266,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":796}	127.0.0.1	Se creó el vale de combustible N.º 2266	2026-10-22 12:56:39	2026-10-22 12:56:39
1781	3	CREAR	vales_combustible	797	\N	{"numero_vale":2267,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":797}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1782	3	CREAR	vales_combustible	797	\N	{"numero_vale":2267,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":797}	127.0.0.1	Se creó el vale de combustible N.º 2267	2026-10-22 12:56:39	2026-10-22 12:56:39
1783	3	CREAR	vales_combustible	798	\N	{"numero_vale":2268,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":798}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1784	3	CREAR	vales_combustible	798	\N	{"numero_vale":2268,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":798}	127.0.0.1	Se creó el vale de combustible N.º 2268	2026-10-22 12:56:39	2026-10-22 12:56:39
1785	3	CREAR	vales_combustible	799	\N	{"numero_vale":2269,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":799}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1786	3	CREAR	vales_combustible	799	\N	{"numero_vale":2269,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":799}	127.0.0.1	Se creó el vale de combustible N.º 2269	2026-10-22 12:56:39	2026-10-22 12:56:39
1787	3	CREAR	vales_combustible	800	\N	{"numero_vale":2270,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":800}	127.0.0.1	Registro creado	2026-10-22 12:56:39	2026-10-22 12:56:39
1788	3	CREAR	vales_combustible	800	\N	{"numero_vale":2270,"anio":2026,"lote_vale_id":3,"periodo_id":10,"entrega_policia_id":4,"usuario_id":3,"apertura_programatica_id":"9","estado":"ENTREGADO","updated_at":"2026-10-22 12:56:39","created_at":"2026-10-22 12:56:39","id":800}	127.0.0.1	Se creó el vale de combustible N.º 2270	2026-10-22 12:56:39	2026-10-22 12:56:39
1789	3	CREAR	vales_combustible	801	\N	{"serie_vale_id":1,"numero_vale":2271,"anio":2026,"fecha":"2026-10-22 00:00:00","vehiculo_id":"3","apertura_programatica_id":1,"kilometraje":"2345","observacion":null,"usuario_id":3,"estado":"EMITIDO","updated_at":"2026-10-22 12:58:45","created_at":"2026-10-22 12:58:45","id":801}	127.0.0.1	Registro creado	2026-10-22 12:58:45	2026-10-22 12:58:45
1790	3	CREAR	vales_combustible	801	\N	{"serie_vale_id":1,"numero_vale":2271,"anio":2026,"fecha":"2026-10-22 00:00:00","vehiculo_id":"3","apertura_programatica_id":1,"kilometraje":"2345","observacion":null,"usuario_id":3,"estado":"EMITIDO","updated_at":"2026-10-22 12:58:45","created_at":"2026-10-22 12:58:45","id":801}	127.0.0.1	Se creó el vale de combustible N.º 2271	2026-10-22 12:58:45	2026-10-22 12:58:45
1791	3	CREAR	vales_combustible	802	\N	{"serie_vale_id":1,"numero_vale":2272,"anio":2026,"fecha":"2026-10-22 00:00:00","vehiculo_id":"11","apertura_programatica_id":1,"kilometraje":"2134","observacion":null,"usuario_id":3,"estado":"EMITIDO","updated_at":"2026-10-22 13:02:14","created_at":"2026-10-22 13:02:14","id":802}	127.0.0.1	Registro creado	2026-10-22 13:02:14	2026-10-22 13:02:14
1792	3	CREAR	vales_combustible	802	\N	{"serie_vale_id":1,"numero_vale":2272,"anio":2026,"fecha":"2026-10-22 00:00:00","vehiculo_id":"11","apertura_programatica_id":1,"kilometraje":"2134","observacion":null,"usuario_id":3,"estado":"EMITIDO","updated_at":"2026-10-22 13:02:14","created_at":"2026-10-22 13:02:14","id":802}	127.0.0.1	Se creó el vale de combustible N.º 2272	2026-10-22 13:02:14	2026-10-22 13:02:14
1793	3	ACTUALIZAR	vales_combustible	802	{"numero_factura":null,"cantidad_litros":null,"precio_unitario":null,"total":null,"updated_at":"2026-10-22T17:02:14.000000Z","estado":"EMITIDO"}	{"numero_factura":"123213","cantidad_litros":"55","precio_unitario":"6.9","total":"379.50","updated_at":"2026-10-22 13:03:28","estado":"COMPLETADO"}	127.0.0.1	Registro actualizado	2026-10-22 13:03:28	2026-10-22 13:03:28
1794	3	MODIFICAR	vales_combustible	802	{"numero_factura":null,"cantidad_litros":null,"precio_unitario":null,"total":null,"estado":"EMITIDO"}	{"numero_factura":"123213","cantidad_litros":"55","precio_unitario":"6.9","total":"379.50","estado":"COMPLETADO"}	127.0.0.1	Se modificó el vale de combustible N.º 2272	2026-10-22 13:03:28	2026-10-22 13:03:28
1796	3	MODIFICAR	vales_combustible	802	{"estacion_servicio_id":null,"tipo_combustible_id":null}	{"estacion_servicio_id":"1","tipo_combustible_id":"2"}	127.0.0.1	Se modificó el vale de combustible N.º 2272	2026-10-22 13:42:26	2026-10-22 13:42:26
1797	3	ACTUALIZAR	vales_combustible	801	{"numero_factura":null,"cantidad_litros":null,"precio_unitario":null,"total":null,"updated_at":"2026-10-22T16:58:45.000000Z","estacion_servicio_id":null,"tipo_combustible_id":null,"estado":"EMITIDO"}	{"numero_factura":"5789","cantidad_litros":"344","precio_unitario":"8","total":"2752.00","updated_at":"2026-10-22 13:43:17","estacion_servicio_id":"1","tipo_combustible_id":"2","estado":"COMPLETADO"}	127.0.0.1	Registro actualizado	2026-10-22 13:43:17	2026-10-22 13:43:17
1798	3	MODIFICAR	vales_combustible	801	{"numero_factura":null,"cantidad_litros":null,"precio_unitario":null,"total":null,"estacion_servicio_id":null,"tipo_combustible_id":null,"estado":"EMITIDO"}	{"numero_factura":"5789","cantidad_litros":"344","precio_unitario":"8","total":"2752.00","estacion_servicio_id":"1","tipo_combustible_id":"2","estado":"COMPLETADO"}	127.0.0.1	Se modificó el vale de combustible N.º 2271	2026-10-22 13:43:17	2026-10-22 13:43:17
\.


--
-- Data for Name: entregas_policia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.entregas_policia (id, lote_vale_id, contrato, dependencia, recibido_por, fecha_entrega, cantidad, numero_inicial, numero_final, solicitud_token, usuario_id, created_at, updated_at) FROM stdin;
4	3	356	Comando Regional de Policía de Sacaba	comandante Luis Gomez	2026-10-22	200	2071	2270	bed24515-37e7-4d83-8e71-3dc8785fce1f	3	2026-10-22 12:56:37	2026-10-22 12:56:37
\.


--
-- Data for Name: estaciones_servicio; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.estaciones_servicio (id, nombre, direccion, telefono, estado, created_at, updated_at) FROM stdin;
1	CITY TRUCK	AV.HUALLYANY	12345454	t	2026-10-22 12:26:47	2026-10-22 12:26:47
2	YPF	AV.CHAPARE	71476393	t	2026-10-22 12:27:19	2026-10-22 12:27:19
\.


--
-- Data for Name: fuentes_financiamiento; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.fuentes_financiamiento (id, codigo, nombre, estado, created_at, updated_at) FROM stdin;
1	20	FF 20	t	2026-10-22 12:20:11	2026-10-22 12:20:11
2	41	FF 41	t	2026-10-22 12:20:11	2026-10-22 12:20:11
\.


--
-- Data for Name: lotes_vales; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lotes_vales (id, numero_inicial, numero_final, fecha_recepcion, periodo_id, estado, created_at, updated_at) FROM stdin;
3	2071	2270	2026-10-22	10	t	2026-10-22 12:48:18	2026-10-22 12:48:18
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2026_09_16_052349_crear_tabla_roles	1
2	2026_09_16_052350_crear_tabla_usuarios	1
3	2026_09_16_052351_crear_tabla_periodos	1
4	2026_09_16_052352_crear_tabla_tipos_combustible	1
5	2026_09_16_052352_crear_tabla_unidades	1
6	2026_09_16_052353_crear_tabla_fuentes_financiamiento	1
7	2026_09_16_052354_crear_tabla_organismos_financiadores	1
8	2026_09_16_052355_crear_tabla_aperturas_programaticas	1
9	2026_09_16_052356_crear_tabla_vehiculos	1
10	2026_09_16_052357_crear_tabla_estaciones_servicio	1
11	2026_09_16_052358_crear_tabla_lotes_vales	1
12	2026_09_16_052358_crear_tabla_vales_combustible	1
13	2026_09_17_003502_crear_tabla_password_reset_tokens	2
14	2026_09_18_025257_crear_tabla_solicitudes_contrasena	3
15	2026_09_26_005429_agregar_campos_a_vales_combustible_table	4
16	2026_09_26_010445_crear_tabla_auditorias	5
17	2026_09_30_012652_permitir_datos_carga_pendientes_en_vales_combustible	6
18	2026_09_30_160918_eliminar_nombre_de_periodos_table	7
19	2026_09_30_160952_eliminar_nombre_de_periodos_table	7
20	2026_09_30_205521_eliminar_nit_de_estaciones_servicio_table	8
21	2026_10_01_120725_hacer_descripcion_opcional_en_aperturas_programaticas	9
22	2026_10_01_145344_eliminar_descripcion_de_fuentes_financiamiento	10
23	2026_10_01_151957_eliminar_descripcion_de_organismos_financiadores	11
24	2026_10_01_000001_numeracion_vales_por_periodo	12
25	2026_10_01_000002_numeracion_anual_seis_digitos	13
26	2026_10_01_000003_entregas_policia	14
27	2026_10_02_000001_precision_litros_vales	15
28	2026_10_11_000001_hacer_datos_perfil_opcionales_en_usuarios	16
29	2026_10_16_203933_remove_tipo_combustible_and_estacion_from_lotes_vales_table	17
30	2026_10_16_215330_convertir_numeracion_vales_a_correlativo_institucional	18
31	2026_10_20_173417_hacer_lote_vale_id_nullable_en_vales_combustible	18
32	2026_10_20_202545_hacer_periodo_id_nullable_en_vales_combustible	19
33	2026_10_21_091329_agregar_apertura_programatica_id_a_vehiculos	20
34	2026_10_21_175821_create_parte_diarios_table	21
35	2026_10_22_112207_cambiar_kilometraje_a_texto_en_vales_combustible	22
\.


--
-- Data for Name: organismos_financiadores; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.organismos_financiadores (id, codigo, nombre, estado, created_at, updated_at) FROM stdin;
1	113	OF 113	t	2026-10-22 12:20:11	2026-10-22 12:20:11
2	119	OF 119	t	2026-10-22 12:20:11	2026-10-22 12:20:11
3	210	OF 210	t	2026-10-22 12:20:11	2026-10-22 12:20:11
\.


--
-- Data for Name: partes_diarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.partes_diarios (id, vehiculo_id, fecha, presentado, observacion, created_at, updated_at) FROM stdin;
1	92	2026-10-22	f	\N	2026-10-22 14:06:31	2026-10-22 14:06:52
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: periodos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.periodos (id, mes, anio, fecha_inicio, fecha_fin, estado, created_at, updated_at) FROM stdin;
1	1	2026	2026-01-01	2026-01-31	t	2026-10-22 12:29:36	2026-10-22 12:29:36
2	2	2026	2026-02-01	2026-02-28	t	2026-10-22 12:29:36	2026-10-22 12:29:36
3	3	2026	2026-03-01	2026-03-31	t	2026-10-22 12:29:36	2026-10-22 12:29:36
4	4	2026	2026-04-01	2026-04-30	t	2026-10-22 12:29:36	2026-10-22 12:29:36
5	5	2026	2026-05-01	2026-05-31	t	2026-10-22 12:29:36	2026-10-22 12:29:36
6	6	2026	2026-06-01	2026-06-30	t	2026-10-22 12:29:36	2026-10-22 12:29:36
7	7	2026	2026-07-01	2026-07-31	t	2026-10-22 12:29:36	2026-10-22 12:29:36
8	8	2026	2026-08-01	2026-08-31	t	2026-10-22 12:29:36	2026-10-22 12:29:36
9	9	2026	2026-09-01	2026-09-30	t	2026-10-22 12:29:36	2026-10-22 12:29:36
10	10	2026	2026-10-01	2026-10-31	t	2026-10-22 12:29:36	2026-10-22 12:29:36
11	11	2026	2026-11-01	2026-11-30	t	2026-10-22 12:29:36	2026-10-22 12:29:36
12	12	2026	2026-12-01	2026-12-31	t	2026-10-22 12:29:36	2026-10-22 12:29:36
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, nombre, descripcion, estado, created_at, updated_at) FROM stdin;
1	ADMINISTRADOR	Encargado de gestionar roles,y función de todo el sistema	t	2026-09-16 06:29:27	2026-10-16 16:07:34
2	AUXILIAR	Encargado de gestionar la gestion de combustible	t	2026-09-17 06:12:10	2026-10-16 16:08:28
\.


--
-- Data for Name: series_vales; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.series_vales (id, nombre, numero_inicial, ultimo_numero, estado, motivo, created_at, updated_at) FROM stdin;
1	SERIE GENERAL SIGECOM	1	2272	t	Continuación de numeración histórica. Último vale anterior: 000200.	2026-10-22 12:38:41	2026-10-22 13:02:14
\.


--
-- Data for Name: solicitudes_contrasena; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.solicitudes_contrasena (id, usuario_id, estado, fecha_atencion, atendido_por, ip_solicitud, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tipos_combustible; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tipos_combustible (id, nombre, unidad_medida, estado, created_at, updated_at) FROM stdin;
1	GASOLINA	Litros	t	2026-10-22 12:25:53	2026-10-22 12:25:53
2	DIESEL	Litros	t	2026-10-22 12:26:06	2026-10-22 12:26:06
\.


--
-- Data for Name: unidades; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.unidades (id, nombre, sigla, tipo, descripcion, estado, created_at, updated_at) FROM stdin;
1	DIRECCION DE MEDIO AMBIENTE Y MADRE TIERRA	\N	\N	\N	t	2026-10-22 12:20:11	2026-10-22 12:20:11
2	FUNCIONAMIENTO ALCALDIA MUNICIPAL	\N	\N	\N	t	2026-10-22 12:20:11	2026-10-22 12:20:11
3	SERVICIOS Y EQUIPAMIENTO DE EDUCACIÓN	\N	\N	\N	t	2026-10-22 12:20:11	2026-10-22 12:20:11
4	UNIDAD DE GESTION DE RIESGOS	\N	\N	\N	t	2026-10-22 12:20:11	2026-10-22 12:20:11
5	UNIDAD DE PARQUES, JARDINES Y SERVICIOS FORESTALES	\N	\N	\N	t	2026-10-22 12:20:11	2026-10-22 12:20:11
6	UNIDAD DE SEGURIDAD CIUDADANA	\N	\N	\N	t	2026-10-22 12:20:11	2026-10-22 12:20:11
7	UNIDAD DE SERVICIOS ELECTRICOS Y SEMAFORIZACION	\N	\N	\N	t	2026-10-22 12:20:11	2026-10-22 12:20:11
8	UNIDAD DE TRANSPORTES Y MAQUINARIAS	\N	\N	\N	t	2026-10-22 12:20:11	2026-10-22 12:20:11
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id, nombre, apellido, ci, correo, contrasena, rol_id, estado, remember_token, created_at, updated_at) FROM stdin;
10	katerin	zuritaa	1234567	katerin16@gmail.com	$2y$12$SpUsdJWA3doX2OizXIhxveNvPMRqxq0K7XkwOpNV.XQ0GDmHPZbpK	2	t	\N	2026-10-16 15:54:27	2026-10-16 16:52:05
4	Katerin	Zrita	123456	katerin@gmail.com	$2y$12$.tr0Ek/5LJT2xy5GJAxHMen9B7EmLgFQj2qeiG/S9jMmluG8jOUim	2	t	n7ZIjyiC1sHvJbapQFaEJPCZApLvsANqe3HuziBWcDtO5wISLT0C3Fqklr1b	2026-09-18 05:42:12	2026-10-16 16:52:13
3	mayte	zurita	67890	mayte@gmail.com	$2y$12$ixiRUHJDq/DwzrVG39GgJeWSj3.YVv8WOvSaQMwYQFYb3kQmWtgAq	1	t	oahRYg9p2KmwxAjN6bZ4ppdP4l4YZ5fhPas0WNqqOVKG5fjoVgD6e1NwzGds	2026-09-17 04:00:07	2026-09-17 04:00:07
\.


--
-- Data for Name: vales_combustible; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vales_combustible (id, numero_vale, fecha, lote_vale_id, vehiculo_id, apertura_programatica_id, usuario_id, numero_factura, cantidad_litros, precio_unitario, total, kilometraje, observacion, created_at, updated_at, estacion_servicio_id, tipo_combustible_id, periodo_id, estado, motivo_anulacion, fecha_anulacion, documento_respaldo, anio, entrega_policia_id, serie_vale_id) FROM stdin;
601	2071	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
602	2072	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
603	2073	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
604	2074	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
605	2075	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
606	2076	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
607	2077	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
608	2078	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
609	2079	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
610	2080	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
611	2081	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
612	2082	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
613	2083	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
614	2084	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:37	2026-10-22 12:56:37	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
615	2085	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
616	2086	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
617	2087	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
618	2088	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
619	2089	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
620	2090	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
621	2091	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
622	2092	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
623	2093	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
624	2094	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
625	2095	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
626	2096	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
627	2097	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
628	2098	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
629	2099	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
630	2100	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
631	2101	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
632	2102	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
633	2103	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
634	2104	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
635	2105	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
636	2106	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
637	2107	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
638	2108	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
639	2109	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
640	2110	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
641	2111	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
642	2112	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
643	2113	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
644	2114	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
645	2115	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
646	2116	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
647	2117	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
648	2118	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
649	2119	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
650	2120	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
651	2121	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
652	2122	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
653	2123	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
654	2124	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
655	2125	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
656	2126	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
657	2127	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
658	2128	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
659	2129	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
660	2130	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
661	2131	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
662	2132	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
663	2133	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
664	2134	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
665	2135	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
666	2136	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
667	2137	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
668	2138	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
669	2139	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
670	2140	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
671	2141	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
672	2142	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
673	2143	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
674	2144	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
675	2145	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
676	2146	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
677	2147	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
678	2148	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
679	2149	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
680	2150	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
681	2151	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
682	2152	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
683	2153	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
684	2154	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
685	2155	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
686	2156	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
687	2157	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
688	2158	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
689	2159	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
690	2160	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
691	2161	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
692	2162	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
693	2163	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
694	2164	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
695	2165	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
696	2166	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
697	2167	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
698	2168	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
699	2169	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
700	2170	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
701	2171	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
702	2172	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
703	2173	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
704	2174	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
705	2175	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
706	2176	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
707	2177	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
708	2178	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
709	2179	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
710	2180	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
711	2181	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
712	2182	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
713	2183	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
714	2184	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
715	2185	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
716	2186	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
717	2187	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
718	2188	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
719	2189	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
720	2190	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
721	2191	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
722	2192	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
723	2193	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
724	2194	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
725	2195	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
726	2196	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
727	2197	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
728	2198	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
729	2199	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
730	2200	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
731	2201	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
732	2202	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
733	2203	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
734	2204	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
735	2205	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
736	2206	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
737	2207	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
738	2208	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
739	2209	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
740	2210	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
741	2211	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
742	2212	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
743	2213	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
744	2214	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
745	2215	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
746	2216	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
747	2217	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
748	2218	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
749	2219	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
750	2220	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
751	2221	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
752	2222	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
753	2223	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
754	2224	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
755	2225	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
756	2226	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
757	2227	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
758	2228	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
759	2229	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
760	2230	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
761	2231	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
762	2232	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
763	2233	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
764	2234	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
765	2235	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
766	2236	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
767	2237	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:38	2026-10-22 12:56:38	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
768	2238	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
769	2239	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
770	2240	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
771	2241	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
772	2242	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
773	2243	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
774	2244	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
775	2245	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
776	2246	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
777	2247	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
778	2248	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
779	2249	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
780	2250	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
781	2251	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
782	2252	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
783	2253	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
784	2254	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
785	2255	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
786	2256	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
787	2257	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
788	2258	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
789	2259	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
790	2260	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
791	2261	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
792	2262	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
793	2263	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
794	2264	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
795	2265	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
796	2266	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
797	2267	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
798	2268	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
799	2269	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
800	2270	\N	3	\N	9	3	\N	\N	\N	\N	\N	\N	2026-10-22 12:56:39	2026-10-22 12:56:39	\N	\N	10	ENTREGADO	\N	\N	\N	2026	4	\N
802	2272	2026-10-22	\N	11	1	3	123213	55.0000000000000	6.9000	379.50	2134	\N	2026-10-22 13:02:14	2026-10-22 13:42:26	1	2	\N	COMPLETADO	\N	\N	\N	2026	\N	1
801	2271	2026-10-22	\N	3	1	3	5789	344.0000000000000	8.0000	2752.00	2345	\N	2026-10-22 12:58:45	2026-10-22 13:43:17	1	2	\N	COMPLETADO	\N	\N	\N	2026	\N	1
\.


--
-- Data for Name: vehiculos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vehiculos (id, codigo, placa, tipo, industria, marca, color, unidad_id, estado, created_at, updated_at, apertura_programatica_id) FROM stdin;
1	B-01	2731NXF	BUS COASTER	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	2
2	C-01	942GLY	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	1
3	C-04	1794HIL	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	1
4	C-05	1882FIH	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	1
5	C-06	2264FHC	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	2
6	C-07	2263XUT	CAMIONETA	\N	\N	\N	7	t	2026-10-22 12:20:11	2026-10-22 12:20:11	5
7	C-08	2295SUT	CAMIONETA	\N	\N	\N	8	t	2026-10-22 12:20:11	2026-10-22 12:20:11	7
8	C-09	2295YTU	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	1
9	C-10	2295YUA	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	1
10	C-11	2838DLK	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	1
11	C-12	2522LIH	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	1
12	C-13	2522LKL	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	2
13	C-15	2859BGB	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	2
14	C-16	2877BLX	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	2
15	C-17	2909YTD	CAMIONETA	\N	\N	\N	8	t	2026-10-22 12:20:11	2026-10-22 12:20:11	7
16	C-18	2909YUG	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	2
17	C-19	2987CSE	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:11	2026-10-22 12:20:11	2
18	C-20	3012ATS	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
19	C-21	3028HND	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
20	C-22	3028HPG	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
21	C-23	3109XLC	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
22	C-24	3115TTT	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
23	C-25	3784ULY	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
24	C-26	3807XLK	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
25	C-27	3807XID	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
26	C-28	3847KAH	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
27	C-29	3992NII	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
28	C-30	4016PNF	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
29	C-32	4034GND	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
30	C-37	4421FYF	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
31	C-38	4533DHT	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
32	C-40	5642XRT	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
33	C-42	5781DLF	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
34	C-44	6283KTH	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
35	C-45	6278GRF	CAMIONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
36	CAR-01	1188CLP	FURGONETA (CARRY)	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
37	CAR-02	1188FCL	FURGONETA (CARRY)	\N	\N	\N	4	t	2026-10-22 12:20:12	2026-10-22 12:20:12	8
38	CAR-03	2568ZZR	FURGONETA (CARRY)	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
39	M-08	1961FXP	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
40	M-16	1511CRU	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
41	M-17	1961FHB	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
42	M-19	1961GBD	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	1
43	M-21	1961FDI	MOTOCICLETA	\N	\N	\N	3	t	2026-10-22 12:20:12	2026-10-22 12:20:12	6
44	M-22	1961FAY	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
45	M-23	1961FCF	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
46	M-24	1961GTH	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
47	M-27	2941LKS	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
48	M-33	2941LDT	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
49	M-34	2776ADE	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
50	M-35	2648NNE	MOTOCICLETA	\N	\N	\N	4	t	2026-10-22 12:20:12	2026-10-22 12:20:12	8
51	M-36	2648NPH	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
52	M-46	3572BHD	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
53	M-49	3668ALK	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
54	M-50	3668AKG	MOTOCICLETA	\N	\N	\N	1	t	2026-10-22 12:20:12	2026-10-22 12:20:12	3
55	M-51	3668AGU	MOTOCICLETA	\N	\N	\N	8	t	2026-10-22 12:20:12	2026-10-22 12:20:12	7
56	M-52	3668AHA	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
57	M-53	3668ADH	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
58	M-54	3668ABB	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
59	M-55	3668ACE	MOTOCICLETA	\N	\N	\N	4	t	2026-10-22 12:20:12	2026-10-22 12:20:12	8
60	M-59	3668AID	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
61	M-61	3689DHK	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	1
62	M-62	3689END	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
63	M-69	3896ZID	MOTOCICLETA	\N	\N	\N	5	t	2026-10-22 12:20:12	2026-10-22 12:20:12	4
64	M-71	4670RPB	MOTOCICLETA	\N	\N	\N	7	t	2026-10-22 12:20:12	2026-10-22 12:20:12	5
65	M-72	5540ARA	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	1
66	M-73	5789ESX	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
67	M-74	5790FFR	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
68	M-78	6132GDI	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
69	M-80	6125ZPD	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
70	M-81	6125ZRG	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
71	M-85	6125XDP	MOTOCICLETA	\N	\N	\N	7	t	2026-10-22 12:20:12	2026-10-22 12:20:12	5
72	M-89	6569FED	MOTOCICLETA	\N	\N	\N	7	t	2026-10-22 12:20:12	2026-10-22 12:20:12	5
73	M-90	6569FFG	MOTOCICLETA	\N	\N	\N	3	t	2026-10-22 12:20:12	2026-10-22 12:20:12	6
74	M-91	6605EKS	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	1
75	M-92	6606PYY	MOTOCICLETA	\N	\N	\N	8	t	2026-10-22 12:20:12	2026-10-22 12:20:12	7
76	M-93	6773YIB	MOTOCICLETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
77	V-04	1193AST	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
78	V-09	3445UGG	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
79	V-13	5744TSS	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
80	V-14	3876YNP	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
81	VD-09	2109KYX	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
82	VD-10	2211RNR	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
83	VD-12	5325DNI	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
84	VD-14	2511CKF	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
85	VD-15	4795GYN	VAGONETA	\N	\N	\N	8	t	2026-10-22 12:20:12	2026-10-22 12:20:12	7
86	VD-16	4124SIT	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	1
87	VD-18	4103CTY	VAGONETA	\N	\N	\N	5	t	2026-10-22 12:20:12	2026-10-22 12:20:12	4
88	VD-22	4556RBI	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
89	VD-23	1708FTS	VAGONETA	\N	\N	\N	8	t	2026-10-22 12:20:12	2026-10-22 12:20:12	7
90	VD-25	2534TCX	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
91	VD-27	4148CHI	VAGONETA	\N	\N	\N	2	t	2026-10-22 12:20:12	2026-10-22 12:20:12	2
92	AP-01	2120CAG	AMBULACIA POLICIAL	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
93	AP-02	5747EIG	AMBULACIA POLICIAL	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
94	CP-02	2101TXC	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
95	CP-03	2101TYF	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
96	CP-04	2263ULT	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
97	CP-06	2893DSC	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
98	CP-07	2893DTF	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
99	CP-08	2981BPP	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
100	CP-09	3063KXF	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
101	CP-10	3063KUC	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
102	CP-11	3063KYI	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	9
103	CP-13	3447DRK	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
104	CP-14	3447DPG	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
105	CP-16	3623RXL	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
106	CP-17	3623RYR	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
107	CP-20	4810CXS	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
108	CP-21	5185XPU	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
109	CP-22	5202TBF	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
110	CP-23	6278GSI	CAMIONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
111	CP-24	6484PCD	VAGONETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
112	MP-01	1961FGX	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
113	MP-21	3171EXX	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
114	MP-26	3171GSL	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
115	MP-27	3171GRH	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
116	MP-34	3310FEA	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
117	MP-37	3310FBL	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
118	MP-38	3310FAH	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
119	MP-41	3533THH	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
120	MP-50	3533TGE	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
121	MP-51	3533TCN	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
122	MP-52	3533TAF	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
123	MP-54	4885RNL	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
124	MP-55	4891ECP	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
125	MP-56	4891EDT	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
126	MP-57	4962DAH	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
127	MP-58	4962DBL	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
128	MP-59	4962DCR	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
129	MP-60	5557XZX	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
130	MP-61	5557YAY	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
131	MP-62	5557XUI	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
132	MP-63	5557XXN	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
133	MP-64	5557XYS	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
134	MP-65	5557YBC	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
135	MP-66	5890GDN	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
136	MP-67	6111CKU	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
137	MP-68	6111CIR	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
138	MP-69	6518HHA	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
139	MP-70	6518HGU	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
140	MP-71	6518HFR	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
141	MP-72	6518HEL	MOTOCICLETA	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
142	VP-03	5221YUH	VAGONETA POLICIAL	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
143	VP-04	5677ZRD	VAGONETA POLICIAL	\N	\N	\N	6	t	2026-10-22 12:20:12	2026-10-22 12:20:12	10
\.


--
-- Name: aperturas_programaticas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.aperturas_programaticas_id_seq', 10, true);


--
-- Name: auditorias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.auditorias_id_seq', 1798, true);


--
-- Name: entregas_policia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.entregas_policia_id_seq', 4, true);


--
-- Name: estaciones_servicio_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.estaciones_servicio_id_seq', 2, true);


--
-- Name: fuentes_financiamiento_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.fuentes_financiamiento_id_seq', 2, true);


--
-- Name: lotes_vales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lotes_vales_id_seq', 3, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 35, true);


--
-- Name: organismos_financiadores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.organismos_financiadores_id_seq', 3, true);


--
-- Name: partes_diarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.partes_diarios_id_seq', 1, true);


--
-- Name: periodos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.periodos_id_seq', 12, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 3, true);


--
-- Name: series_vales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.series_vales_id_seq', 1, true);


--
-- Name: solicitudes_contrasena_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.solicitudes_contrasena_id_seq', 1, false);


--
-- Name: tipos_combustible_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tipos_combustible_id_seq', 2, true);


--
-- Name: unidades_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.unidades_id_seq', 8, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 10, true);


--
-- Name: vales_combustible_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vales_combustible_id_seq', 802, true);


--
-- Name: vehiculos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vehiculos_id_seq', 143, true);


--
-- Name: aperturas_programaticas aperturas_programaticas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aperturas_programaticas
    ADD CONSTRAINT aperturas_programaticas_pkey PRIMARY KEY (id);


--
-- Name: auditorias auditorias_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditorias
    ADD CONSTRAINT auditorias_pkey PRIMARY KEY (id);


--
-- Name: entregas_policia entregas_policia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entregas_policia
    ADD CONSTRAINT entregas_policia_pkey PRIMARY KEY (id);


--
-- Name: entregas_policia entregas_policia_solicitud_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entregas_policia
    ADD CONSTRAINT entregas_policia_solicitud_token_unique UNIQUE (solicitud_token);


--
-- Name: estaciones_servicio estaciones_servicio_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estaciones_servicio
    ADD CONSTRAINT estaciones_servicio_pkey PRIMARY KEY (id);


--
-- Name: fuentes_financiamiento fuentes_financiamiento_codigo_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fuentes_financiamiento
    ADD CONSTRAINT fuentes_financiamiento_codigo_unique UNIQUE (codigo);


--
-- Name: fuentes_financiamiento fuentes_financiamiento_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fuentes_financiamiento
    ADD CONSTRAINT fuentes_financiamiento_pkey PRIMARY KEY (id);


--
-- Name: lotes_vales lotes_vales_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotes_vales
    ADD CONSTRAINT lotes_vales_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: organismos_financiadores organismos_financiadores_codigo_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.organismos_financiadores
    ADD CONSTRAINT organismos_financiadores_codigo_unique UNIQUE (codigo);


--
-- Name: organismos_financiadores organismos_financiadores_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.organismos_financiadores
    ADD CONSTRAINT organismos_financiadores_pkey PRIMARY KEY (id);


--
-- Name: partes_diarios partes_diarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partes_diarios
    ADD CONSTRAINT partes_diarios_pkey PRIMARY KEY (id);


--
-- Name: partes_diarios partes_diarios_vehiculo_fecha_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partes_diarios
    ADD CONSTRAINT partes_diarios_vehiculo_fecha_unique UNIQUE (vehiculo_id, fecha);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: periodos periodos_mes_anio_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.periodos
    ADD CONSTRAINT periodos_mes_anio_unique UNIQUE (mes, anio);


--
-- Name: periodos periodos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.periodos
    ADD CONSTRAINT periodos_pkey PRIMARY KEY (id);


--
-- Name: roles roles_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_nombre_unique UNIQUE (nombre);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: series_vales series_vales_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.series_vales
    ADD CONSTRAINT series_vales_pkey PRIMARY KEY (id);


--
-- Name: solicitudes_contrasena solicitudes_contrasena_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.solicitudes_contrasena
    ADD CONSTRAINT solicitudes_contrasena_pkey PRIMARY KEY (id);


--
-- Name: tipos_combustible tipos_combustible_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipos_combustible
    ADD CONSTRAINT tipos_combustible_nombre_unique UNIQUE (nombre);


--
-- Name: tipos_combustible tipos_combustible_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipos_combustible
    ADD CONSTRAINT tipos_combustible_pkey PRIMARY KEY (id);


--
-- Name: unidades unidades_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unidades
    ADD CONSTRAINT unidades_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_ci_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_ci_unique UNIQUE (ci);


--
-- Name: usuarios usuarios_correo_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_correo_unique UNIQUE (correo);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: vales_combustible vales_combustible_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_pkey PRIMARY KEY (id);


--
-- Name: vales_combustible vales_serie_numero_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_serie_numero_unique UNIQUE (serie_vale_id, numero_vale);


--
-- Name: vehiculos vehiculos_codigo_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_codigo_unique UNIQUE (codigo);


--
-- Name: vehiculos vehiculos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_pkey PRIMARY KEY (id);


--
-- Name: vehiculos vehiculos_placa_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_placa_unique UNIQUE (placa);


--
-- Name: aperturas_programaticas aperturas_programaticas_fuente_financiamiento_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aperturas_programaticas
    ADD CONSTRAINT aperturas_programaticas_fuente_financiamiento_id_foreign FOREIGN KEY (fuente_financiamiento_id) REFERENCES public.fuentes_financiamiento(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: aperturas_programaticas aperturas_programaticas_organismo_financiador_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aperturas_programaticas
    ADD CONSTRAINT aperturas_programaticas_organismo_financiador_id_foreign FOREIGN KEY (organismo_financiador_id) REFERENCES public.organismos_financiadores(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: auditorias auditorias_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditorias
    ADD CONSTRAINT auditorias_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: entregas_policia entregas_policia_lote_vale_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entregas_policia
    ADD CONSTRAINT entregas_policia_lote_vale_id_foreign FOREIGN KEY (lote_vale_id) REFERENCES public.lotes_vales(id) ON DELETE RESTRICT;


--
-- Name: entregas_policia entregas_policia_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entregas_policia
    ADD CONSTRAINT entregas_policia_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE RESTRICT;


--
-- Name: lotes_vales lotes_vales_periodo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotes_vales
    ADD CONSTRAINT lotes_vales_periodo_id_foreign FOREIGN KEY (periodo_id) REFERENCES public.periodos(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: partes_diarios partes_diarios_vehiculo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partes_diarios
    ADD CONSTRAINT partes_diarios_vehiculo_id_foreign FOREIGN KEY (vehiculo_id) REFERENCES public.vehiculos(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: solicitudes_contrasena solicitudes_contrasena_atendido_por_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.solicitudes_contrasena
    ADD CONSTRAINT solicitudes_contrasena_atendido_por_foreign FOREIGN KEY (atendido_por) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: solicitudes_contrasena solicitudes_contrasena_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.solicitudes_contrasena
    ADD CONSTRAINT solicitudes_contrasena_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: usuarios usuarios_rol_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_rol_id_foreign FOREIGN KEY (rol_id) REFERENCES public.roles(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_apertura_programatica_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_apertura_programatica_id_foreign FOREIGN KEY (apertura_programatica_id) REFERENCES public.aperturas_programaticas(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_entrega_policia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_entrega_policia_id_foreign FOREIGN KEY (entrega_policia_id) REFERENCES public.entregas_policia(id) ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_estacion_servicio_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_estacion_servicio_id_foreign FOREIGN KEY (estacion_servicio_id) REFERENCES public.estaciones_servicio(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_lote_vale_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_lote_vale_id_foreign FOREIGN KEY (lote_vale_id) REFERENCES public.lotes_vales(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_periodo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_periodo_id_foreign FOREIGN KEY (periodo_id) REFERENCES public.periodos(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_serie_vale_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_serie_vale_id_foreign FOREIGN KEY (serie_vale_id) REFERENCES public.series_vales(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_tipo_combustible_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_tipo_combustible_id_foreign FOREIGN KEY (tipo_combustible_id) REFERENCES public.tipos_combustible(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vales_combustible vales_combustible_vehiculo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vales_combustible
    ADD CONSTRAINT vales_combustible_vehiculo_id_foreign FOREIGN KEY (vehiculo_id) REFERENCES public.vehiculos(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vehiculos vehiculos_apertura_programatica_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_apertura_programatica_id_foreign FOREIGN KEY (apertura_programatica_id) REFERENCES public.aperturas_programaticas(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vehiculos vehiculos_unidad_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_unidad_id_foreign FOREIGN KEY (unidad_id) REFERENCES public.unidades(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

\unrestrict DVHGTLajKHfyTYg63CaHzqCAadXQztsbl09yFl2UOcUKIUUlKKf7ygLGyGOQWiL

