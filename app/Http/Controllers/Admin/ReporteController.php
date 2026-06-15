<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\ReportPdf;
use App\Models\Asignacion;
use App\Models\Estudiante;
use App\Models\Calificacion;
use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Docente;
use App\Models\AnioLectivo;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $cursos = Curso::where('activo', true)->get();
        $docentes = Docente::with('usuario')->where('activo', true)->get();
        $materias = Materia::where('activo', true)->get();
        $anios = AnioLectivo::where('activo', true)->get();
        $asignaciones = Asignacion::with(['docente.usuario', 'materia', 'curso', 'anioLectivo'])
            ->where('activo', true)->get();

        $totalEstudiantes = Estudiante::where('activo', true)->count();
        $totalAsignaciones = $asignaciones->count();
        $totalCursos = $cursos->count();
        $totalDocentes = $docentes->count();

        $asignacionesPorCurso = $asignaciones->groupBy('id_curso')->map(function ($items, $key) use ($cursos) {
            $curso = $cursos->firstWhere('id_curso', $key);
            return [
                'curso' => $curso->nombre ?? '—',
                'total' => $items->count(),
                'docentes' => $items->pluck('docente.usuario.nombre')->unique()->implode(', '),
                'materias' => $items->pluck('materia.nombre')->unique()->implode(', '),
            ];
        });

        $asignacionesPorDocente = $asignaciones->groupBy('id_docente')->map(function ($items, $key) use ($docentes) {
            $docente = $docentes->firstWhere('id_docente', $key);
            return [
                'docente' => $docente->usuario->nombre ?? '—',
                'total' => $items->count(),
                'cursos' => $items->pluck('curso.nombre')->unique()->implode(', '),
            ];
        });

        $horarioPorCurso = null;
        if ($request->filled('curso_horario')) {
            $horarioPorCurso = Horario::with('asignacion.docente.usuario', 'asignacion.materia', 'asignacion.curso')
                ->where('activo', true)
                ->whereHas('asignacion', function ($q) use ($request) {
                    $q->where('id_curso', $request->curso_horario);
                })
                ->get()
                ->groupBy('dia_semana');
        }

        return view('admin.reportes.index', compact(
            'cursos', 'docentes', 'materias', 'anios', 'asignaciones',
            'totalEstudiantes', 'totalAsignaciones', 'totalCursos', 'totalDocentes',
            'asignacionesPorCurso', 'asignacionesPorDocente', 'horarioPorCurso'
        ));
    }

    public function estudiantes()
    {
        $estudiantes = Estudiante::with(['usuario', 'curso.nivel'])
            ->where('activo', true)
            ->orderBy('codigo_estudiante')
            ->get();

        $pdf = new ReportPdf('Listado General de Estudiantes');
        $pdf->AddPage();

        $pdf->InfoBlock([
            'Total' => $estudiantes->count() . ' estudiantes activos',
            'Fecha' => date('d/m/Y H:i'),
        ]);

        $headers = ['Código', 'Nombres', 'Apellidos', 'Curso', 'Género'];
        $w = [30, 55, 55, 60, 20];
        $pdf->ColoredTableHeader($headers, $w);

        $fill = false;
        foreach ($estudiantes as $e) {
            try {
                $nombre = $e->usuario->nombre ?? '—';
                $apellido = $e->usuario->apellido ?? '—';
            } catch (\Exception $ex) {
                $nombre = '—';
                $apellido = '—';
            }
            $curso = $e->curso ? $e->curso->nombre : '—';
            $genero = $e->genero ?? '—';

            $this->drawRow($pdf, [$e->codigo_estudiante, $nombre, $apellido, $curso, $genero], $w, $fill);
            $fill = !$fill;
        }

        $pdf->Output('D', 'estudiantes.pdf');
        exit;
    }

    public function calificaciones($idAsignacion)
    {
        $asignacion = Asignacion::with(['materia', 'curso', 'docente.usuario', 'anioLectivo'])
            ->findOrFail($idAsignacion);

        $calificaciones = Calificacion::with(['estudiante.usuario', 'periodo', 'tipoEvaluacion'])
            ->where('id_asignacion', $idAsignacion)
            ->orderBy('id_estudiante')
            ->get()
            ->groupBy('id_estudiante');

        $periodos = $calificaciones->flatMap(function ($items) {
            return $items->pluck('periodo.nombre')->unique();
        })->unique();

        $pdf = new ReportPdf('Reporte de Calificaciones');
        $pdf->AddPage();

        try {
            $docNombre = $asignacion->docente->usuario->nombre . ' ' . $asignacion->docente->usuario->apellido;
        } catch (\Exception $e) {
            $docNombre = '—';
        }

        $pdf->InfoBlock([
            'Código' => $asignacion->codigo ?? '—',
            'Materia' => $asignacion->materia->nombre ?? '—',
            'Curso' => $asignacion->curso->nombre ?? '—',
            'Docente' => $docNombre,
            'Período Lectivo' => $asignacion->anioLectivo->anio ?? '—',
        ]);

        $headers = ['Estudiante'];
        $w = [70];
        foreach ($periodos as $p) {
            $headers[] = $p;
            $w[] = 32;
        }
        $headers[] = 'Promedio';
        $w[] = 27;

        $pdf->ColoredTableHeader($headers, $w);

        $fill = false;
        foreach ($calificaciones as $estId => $items) {
            $est = $items->first()->estudiante;
            try {
                $estNombre = ($est->usuario->nombre ?? '') . ' ' . ($est->usuario->apellido ?? '');
            } catch (\Exception $e) {
                $estNombre = '—';
            }

            $suma = 0;
            $count = 0;
            $notasValores = [];
            foreach ($periodos as $p) {
                $nota = $items->firstWhere('periodo.nombre', $p);
                $val = $nota ? number_format($nota->nota, 1) : '—';
                $notasValores[] = $val;
                if ($nota) {
                    $suma += $nota->nota;
                    $count++;
                }
            }
            $prom = $count > 0 ? number_format($suma / $count, 1) : '—';
            $promNum = $count > 0 ? ($suma / $count) : 0;

            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
                $pdf->SetTextColor(50, 50, 50);
            } else {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(50, 50, 50);
            }

            $pdf->Cell($w[0], 6, $estNombre, 1, 0, 'L', $fill);
            foreach ($notasValores as $i => $val) {
                $notaNum = is_numeric($val) ? (float)$val : 0;
                $color = $notaNum >= 70 ? [46, 125, 50] : ($notaNum >= 40 ? [230, 81, 0] : [198, 40, 40]);
                $pdf->SetTextColor($color[0], $color[1], $color[2]);
                $pdf->Cell($w[$i + 1], 6, $val, 1, 0, 'C', $fill);
                $pdf->SetTextColor(50, 50, 50);
            }

            $pdf->SetFont('Arial', 'B', 9);
            $promColor = $promNum >= 70 ? [46, 125, 50] : ($promNum >= 40 ? [230, 81, 0] : [198, 40, 40]);
            $pdf->SetTextColor($promColor[0], $promColor[1], $promColor[2]);
            $pdf->Cell($w[count($w) - 1], 6, $prom, 1, 0, 'C', $fill);

            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Ln();

            $fill = !$fill;
        }

        $pdf->Output('D', 'calificaciones_' . $idAsignacion . '.pdf');
        exit;
    }

    public function asistencia($idAsignacion)
    {
        $asignacion = Asignacion::with(['materia', 'curso', 'docente.usuario'])
            ->findOrFail($idAsignacion);

        $asistencias = Asistencia::with('estudiante.usuario')
            ->where('id_asignacion', $idAsignacion)
            ->orderBy('fecha')
            ->get()
            ->groupBy('id_estudiante');

        $fechas = Asistencia::where('id_asignacion', $idAsignacion)
            ->select('fecha')
            ->distinct()
            ->orderBy('fecha')
            ->get()
            ->pluck('fecha');

        $totalFechas = $fechas->count();
        $maxEstName = 45;
        $colAncho = min(18, (277 - $maxEstName - 20) / max($totalFechas, 1));

        $pdf = new ReportPdf('Reporte de Asistencia');
        $pdf->AddPage();

        $pdf->InfoBlock([
            'Código' => $asignacion->codigo ?? '—',
            'Materia' => $asignacion->materia->nombre ?? '—',
            'Curso' => $asignacion->curso->nombre ?? '—',
            'Docente' => ($asignacion->docente->usuario->nombre ?? '') . ' ' . ($asignacion->docente->usuario->apellido ?? ''),
        ]);

        $headers = ['Estudiante'];
        $w = [$maxEstName];
        foreach ($fechas as $f) {
            $headers[] = \Carbon\Carbon::parse($f)->format('d/m');
            $w[] = $colAncho;
        }
        $headers[] = '% Asist.';
        $w[] = 20;

        $pdf->ColoredTableHeader($headers, $w);
        $pdf->SetFont('Arial', '', 8);

        $fill = false;
        foreach ($asistencias as $estId => $items) {
            $est = $items->first()->estudiante;
            try {
                $estNombre = ($est->usuario->nombre ?? '') . ' ' . ($est->usuario->apellido ?? '');
            } catch (\Exception $e) {
                $estNombre = '—';
            }
            if (strlen($estNombre) > 35) $estNombre = substr($estNombre, 0, 33) . '...';

            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }

            $pdf->Cell($w[0], 6, $estNombre, 1, 0, 'L', $fill);

            $presentes = 0;
            $total = 0;
            foreach ($fechas as $f) {
                $asis = $items->firstWhere('fecha', $f->format('Y-m-d'));
                $estado = $asis ? $asis->estado : '—';
                $letra = strtoupper(substr($estado, 0, 1));

                switch ($estado) {
                    case 'presente':
                        $pdf->SetTextColor(46, 125, 50);
                        break;
                    case 'ausente':
                        $pdf->SetTextColor(198, 40, 40);
                        break;
                    case 'tardanza':
                        $pdf->SetTextColor(230, 81, 0);
                        break;
                    default:
                        $pdf->SetTextColor(180, 180, 180);
                }

                $pdf->Cell($colAncho, 6, $letra, 1, 0, 'C', $fill);
                $pdf->SetTextColor(50, 50, 50);

                if ($asis) {
                    $total++;
                    if ($asis->estado === 'presente') $presentes++;
                }
            }

            $porcentaje = $total > 0 ? number_format(($presentes / $total) * 100, 1) . '%' : '—';
            $pctNum = $total > 0 ? ($presentes / $total) * 100 : 0;
            $pdf->SetTextColor($pctNum >= 75 ? [46, 125, 50] : ($pctNum >= 50 ? [230, 81, 0] : [198, 40, 40]));
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(20, 6, $porcentaje, 1, 0, 'C', $fill);

            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Ln();
            $fill = !$fill;
        }

        $pdf->Output('D', 'asistencia_' . $idAsignacion . '.pdf');
        exit;
    }

    public function horarioPdf($idCurso)
    {
        $curso = Curso::findOrFail($idCurso);
        $horarios = Horario::with('asignacion.docente.usuario', 'asignacion.materia', 'asignacion.curso')
            ->where('activo', true)
            ->whereHas('asignacion', function ($q) use ($idCurso) {
                $q->where('id_curso', $idCurso);
            })
            ->get()
            ->groupBy('dia_semana');

        $diasOrden = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $diasDisplay = ['LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'];
        $franjas = ['14:00', '14:40', '15:20', '16:30', '17:10'];
        $franjasLabel = ['14:00 - 14:40', '14:40 - 15:20', '15:20 - 16:00', '16:30 - 17:10', '17:10 - 17:50'];

        $pdf = new ReportPdf('Horario de Clases - ' . $curso->nombre, true);
        $pdf->AddPage();

        $pdf->InfoBlock([
            'Curso' => $curso->nombre,
            'Horario' => 'Lunes a Sábado · 14:00 - 17:50',
            'Recreo' => '16:00 - 16:30',
            'Períodos' => '5 períodos de 40 min c/u',
        ]);

        $timeW = 26;
        $colW = 42;
        $rowH = 22;
        $totalW = $timeW + ($colW * 6);
        $startX = ($pdf->GetPageWidth() - $totalW) / 2;
        $startY = $pdf->GetY();

        $dayColors = [
            [30, 60, 114],
            [46, 125, 50],
            [198, 40, 40],
            [106, 27, 154],
            [0, 105, 92],
            [230, 81, 0],
        ];

        $dayBgColors = [
            [235, 241, 255],
            [232, 245, 233],
            [255, 235, 238],
            [243, 229, 245],
            [224, 247, 250],
            [255, 243, 224],
        ];

        $pdf->SetFont('Arial', 'B', 7.5);
        $pdf->SetFillColor(30, 60, 114);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(30, 60, 114);

        $x = $startX;
        $y = $startY;
        $pdf->Rect($x, $y, $timeW, 8, 'DF');
        $pdf->SetXY($x, $y);
        $pdf->Cell($timeW, 8, 'HORA', 1, 0, 'C', true);

        $x += $timeW;
        foreach ($diasOrden as $di => $day) {
            $pdf->SetFillColor($dayColors[$di][0], $dayColors[$di][1], $dayColors[$di][2]);
            $pdf->Rect($x, $y, $colW, 8, 'DF');
            $pdf->SetXY($x, $y);
            $pdf->Cell($colW, 8, $diasDisplay[$di], 1, 0, 'C', true);
            $x += $colW;
        }
        $pdf->SetY($y + 8);

        $pdf->SetDrawColor(200, 210, 220);
        $fill = false;

        foreach ($franjas as $fi => $franja) {
            $y = $pdf->GetY();
            $x = $startX;

            if ($fi === 3) {
                $recesoY = $y;
                $pdf->SetFillColor(255, 248, 225);
                $pdf->SetTextColor(180, 120, 0);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Rect($x, $recesoY, $totalW, 7, 'DF');
                $pdf->SetXY($x, $recesoY);
                $pdf->Cell($totalW, 7, 'R E C R E O  —  16:00 a 16:30', 1, 1, 'C', true);
                $pdf->SetTextColor(50, 50, 50);
            }

            $y = $pdf->GetY();
            $x = $startX;

            $pdf->SetFont('Arial', 'B', 7.5);
            $pdf->SetFillColor(235, 238, 245);
            $pdf->SetTextColor(30, 60, 114);
            $pdf->Rect($x, $y, $timeW, $rowH, 'DF');
            $pdf->SetXY($x, $y + ($rowH / 2) - 3);
            $pdf->Cell($timeW, 6, $franjasLabel[$fi], 0, 0, 'C');
            $pdf->SetTextColor(50, 50, 50);
            $x += $timeW;

            foreach ($diasOrden as $di => $day) {
                $clases = collect($horarios->get($day, []))->filter(function ($h) use ($franja) {
                    return substr($h->hora_inicio, 0, 5) === $franja;
                });

                $lines = [];
                foreach ($clases as $c) {
                    $lines[] = $c->asignacion->codigo ?? '';
                    $lines[] = $c->asignacion->materia->nombre ?? '';
                    $lines[] = $c->asignacion->docente->usuario->nombre ?? '';
                }

                $pdf->DrawScheduleCell($x, $y, $colW, $rowH, $lines, $dayColors[$di], $fill);
                $x += $colW;
            }

            $pdf->SetY($y + $rowH);
            $fill = !$fill;
        }

        $pdf->Output('D', 'horario_' . $idCurso . '.pdf');
        exit;
    }

    private function drawRow($pdf, $data, $widths, $fill = false)
    {
        if ($fill) {
            $pdf->SetFillColor(245, 247, 250);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }
        $pdf->SetTextColor(50, 50, 50);
        foreach ($data as $i => $val) {
            $align = ($i === 0 || $i === 4) ? 'C' : 'L';
            $pdf->Cell($widths[$i], 6, $val, 1, 0, $align, $fill);
        }
        $pdf->Ln();
    }
}
