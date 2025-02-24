import PrimaryButton from '@/Components/PrimaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

type PassportApplication = {
    workflow_id: string;
    status: string;
};

type Props = {
    passport_applications: PassportApplication[];
};

export default function Dashboard(props: Props) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="border border-gray-100 p-6 text-gray-900 dark:text-gray-100">
                            <table className="table-auto border-collapse border border-gray-400">
                                <thead>
                                    <tr>
                                        <th className="border border-gray-300 p-2">
                                            Workflow ID
                                        </th>
                                        <th className="border border-gray-300 p-2">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {props.passport_applications.map(
                                        (entry) => (
                                            <tr key={entry.workflow_id}>
                                                <td className="border border-gray-300 p-2">
                                                    <Link
                                                        href={route(
                                                            'passport.first-page.view',
                                                            {
                                                                workflow_id:
                                                                    entry.workflow_id,
                                                            },
                                                        )}
                                                    >
                                                        {entry.workflow_id}
                                                    </Link>
                                                </td>
                                                <td className="border border-gray-300 p-2">
                                                    {entry.status}
                                                </td>
                                            </tr>
                                        ),
                                    )}
                                </tbody>
                            </table>
                        </div>
                        <div className="p-6">
                            <Link href={route('passport.apply')}>
                                <PrimaryButton>Apply Passport</PrimaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
